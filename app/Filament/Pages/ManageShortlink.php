<?php

namespace App\Filament\Pages;

use App\Models\Shortlink;
use App\Models\ShortlinkLead;
use BackedEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/* ===========================
| FILAMENT CORE
=========================== */
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;

/* ===========================
| SCHEMA & FORM
=========================== */
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

/* ===========================
| FORM COMPONENTS
=========================== */
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;

/* ===========================
| TABLE & WIDGET
=========================== */
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class ManageShortlink extends Page
{
    protected string $view = 'filament.pages.manage-shortlink';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-link';
    protected static ?string $title = 'Kelola Shortlink & Barcode';
    protected static ?string $navigationLabel = 'Shortlink & Barcode';
    protected static ?int $navigationSort = 1;

    /**
     * Hanya admin dan user dengan role 'shortlink' yang boleh mengakses halaman ini
     */
    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'shortlink']);
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'is_capture_active' => false,
            'capture_fields'    => ['nama', 'whatsapp'],
            'is_active'         => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('🔗 Buat Shortlink & Barcode Pegawai')
                        ->description('Generate shortlink 5 karakter acak beserta QR Code/Barcode untuk pegawai.')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('pegawai_name')
                                    ->label('Nama Pegawai / Pemilik Link')
                                    ->placeholder('Contoh: Budi Santoso, S.Kom')
                                    ->prefixIcon('heroicon-m-user')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('destination_url')
                                    ->label('Tautan Tujuan Asli (Target URL)')
                                    ->placeholder('https://contoh-link.com/form-atau-drive')
                                    ->prefixIcon('heroicon-m-globe-alt')
                                    ->url()
                                    ->required()
                                    ->maxLength(1000),
                            ]),

                            Grid::make(2)->schema([
                                Toggle::make('is_capture_active')
                                    ->label('Aktifkan Formulir Pengambilan Data (Leads)')
                                    ->helperText('Jika ON, pengunjung yang scan/buka link harus isi data terlebih dahulu sebelum menuju tautan.')
                                    ->live()
                                    ->default(false),

                                CheckboxList::make('capture_fields')
                                    ->label('Pilihan Data yang Wajib Diisi Pengunjung')
                                    ->options([
                                        'nama'     => '👤 Nama Lengkap',
                                        'whatsapp' => '📱 Nomor WhatsApp',
                                        'email'    => '✉️ Alamat Email',
                                    ])
                                    ->visible(fn(callable $get) => $get('is_capture_active'))
                                    ->columns(3)
                                    ->required(fn(callable $get) => $get('is_capture_active')),
                            ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('⚡ Generate Shortlink & Barcode')
                                ->submit('save')
                                ->color('primary'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Shortlink::create([
            'pegawai_name'      => $state['pegawai_name'],
            'code'              => Shortlink::generateUniqueCode(5),
            'destination_url'   => $state['destination_url'],
            'is_capture_active' => $state['is_capture_active'] ?? false,
            'capture_fields'    => !empty($state['is_capture_active']) ? ($state['capture_fields'] ?? []) : null,
            'is_active'         => true,
            'created_by'        => Auth::id(),
        ]);

        $this->form->fill([
            'is_capture_active' => false,
            'capture_fields'    => ['nama', 'whatsapp'],
            'is_active'         => true,
        ]);

        $this->dispatch('refreshShortlinkTables');

        Notification::make()
            ->title('Shortlink & Barcode Berhasil Dibuat!')
            ->body('Kode 5 karakter dan Barcode siap dibagikan.')
            ->success()
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            DaftarShortlinkTable::class,
            DaftarLeadsTable::class,
        ];
    }
}

/**
 * =========================================================
 * WIDGET: TABEL SHORTLINK & BARCODE
 * =========================================================
 */
class DaftarShortlinkTable extends TableWidget
{
    protected static ?string $heading = '📋 Daftar Shortlink Pegawai & Barcode';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshShortlinkTables' => '$refresh'];

    public function table(Table $table): Table
    {
        // 🔒 Superadmin (admin) melihat semua data, adminshortlink hanya melihat miliknya sendiri
        $query = Shortlink::query()->with(['user'])->withCount('leads')->latest();

        if (Auth::user()?->role !== 'admin') {
            $query->where('created_by', Auth::id());
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('pegawai_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->badge()
                    ->color('gray')
                    ->visible(fn() => Auth::user()?->role === 'admin')
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Shortlink')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn(string $state): string => url('/s/' . $state))
                    ->copyable()
                    ->copyMessage('Shortlink berhasil disalin!')
                    ->copyableState(fn(Shortlink $record): string => $record->short_url),

                TextColumn::make('destination_url')
                    ->label('Tujuan Asli')
                    ->limit(30)
                    ->tooltip(fn(Shortlink $record): string => $record->destination_url)
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray'),

                TextColumn::make('clicks_count')
                    ->label('Total Klik')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('leads_count')
                    ->label('Data Masuk')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_capture_active')
                    ->label('Ambil Data')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('capture_fields')
                    ->label('Field Diminta')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($state, Shortlink $record) {
                        if (! $record->is_capture_active) {
                            return '-';
                        }

                        $labels = [
                            'nama'     => 'Nama',
                            'whatsapp' => 'WhatsApp',
                            'email'    => 'Email',
                        ];

                        if (is_string($state)) {
                            $lower = strtolower(trim($state));
                            return $labels[$lower] ?? ucfirst($lower);
                        }

                        return $state;
                    }),
            ])
            ->headerActions([
                // 1. Download Template Excel/CSV
                Action::make('download_template')
                    ->label('Template Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->tooltip('Unduh template Excel / CSV untuk import data massal')
                    ->url(route('admin.shortlink.template'))
                    ->openUrlInNewTab(false),

                // 2. Export Data Shortlink ke Excel/CSV
                Action::make('export_shortlinks')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->tooltip('Unduh semua data shortlink & barcode ke file Excel / CSV')
                    ->url(route('admin.shortlink.export'))
                    ->openUrlInNewTab(false),

                // 3. Import Data Shortlink dari File Excel/CSV
                Action::make('import_shortlinks')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->modalHeading('📥 Import Data Shortlink & Barcode')
                    ->modalDescription('Unggah file Excel / CSV sesuai format template. Kode shortlink 5 karakter dan barcode akan dibuat otomatis.')
                    ->modalSubmitActionLabel('Mulai Import')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pilih File Excel / CSV')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                            ->disk('local')
                            ->directory('temp_imports')
                            ->required()
                            ->helperText('Gunakan file template yang diunduh dari tombol "Template Excel".'),
                    ])
                    ->action(function (array $data) {
                        $disk = Storage::disk('local');
                        $fileKey = $data['file'];

                        // Cari file di disk local atau root storage path
                        $fileContent = null;
                        if ($disk->exists($fileKey)) {
                            $fileContent = $disk->get($fileKey);
                            $disk->delete($fileKey);
                        } elseif (file_exists(storage_path('app/' . $fileKey))) {
                            $fileContent = file_get_contents(storage_path('app/' . $fileKey));
                            @unlink(storage_path('app/' . $fileKey));
                        } elseif (file_exists(storage_path('app/private/' . $fileKey))) {
                            $fileContent = file_get_contents(storage_path('app/private/' . $fileKey));
                            @unlink(storage_path('app/private/' . $fileKey));
                        }

                        if ($fileContent === null) {
                            Notification::make()
                                ->title('Gagal mengimpor data')
                                ->body('File unggahan tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Parse isi file baris per baris
                        $lines = preg_split("/\r\n|\n|\r/", trim($fileContent));
                        $rows = array_filter(array_map(function ($line) {
                            return str_getcsv($line, ';');
                        }, $lines));

                        // Jika format memakai koma biasa alih-alih titik koma
                        if (isset($rows[0]) && count($rows[0]) === 1) {
                            $rows = array_filter(array_map(function ($line) {
                                return str_getcsv($line, ',');
                            }, $lines));
                        }

                        if (empty($rows)) {
                            Notification::make()
                                ->title('File Kosong')
                                ->body('File yang diunggah tidak memiliki data.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Buang header
                        array_shift($rows);

                        $insertedCount = 0;
                        $userId = Auth::id();

                        foreach ($rows as $row) {
                            $pegawaiName = isset($row[0]) ? trim($row[0]) : '';
                            $destinationUrl = isset($row[1]) ? trim($row[1]) : '';
                            $isCaptureRaw = isset($row[2]) ? strtoupper(trim($row[2])) : 'TIDAK';
                            $captureFieldsRaw = isset($row[3]) ? trim($row[3]) : '';

                            if (empty($pegawaiName) || empty($destinationUrl)) {
                                continue;
                            }

                            if (! str_starts_with($destinationUrl, 'http://') && ! str_starts_with($destinationUrl, 'https://')) {
                                $destinationUrl = 'https://' . $destinationUrl;
                            }

                            $isCaptureActive = in_array($isCaptureRaw, ['YA', 'YES', '1', 'TRUE', 'AKTIF']);

                            $captureFields = [];
                            if ($isCaptureActive && ! empty($captureFieldsRaw)) {
                                $rawList = explode(',', $captureFieldsRaw);
                                foreach ($rawList as $field) {
                                    $cleaned = strtolower(trim($field));
                                    if (in_array($cleaned, ['nama', 'whatsapp', 'email'])) {
                                        $captureFields[] = $cleaned;
                                    }
                                }
                            }

                            if ($isCaptureActive && empty($captureFields)) {
                                $captureFields = ['nama', 'whatsapp'];
                            }

                            Shortlink::create([
                                'pegawai_name'      => $pegawaiName,
                                'code'              => Shortlink::generateUniqueCode(5),
                                'destination_url'   => $destinationUrl,
                                'is_capture_active' => $isCaptureActive,
                                'capture_fields'    => $captureFields,
                                'is_active'         => true,
                                'created_by'        => $userId,
                            ]);

                            $insertedCount++;
                        }

                        Notification::make()
                            ->title('Import Data Berhasil!')
                            ->body("Sebanyak {$insertedCount} shortlink & barcode baru telah otomatis dibuat.")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                // 1. Tombol Modal Pratinjau Barcode / QR Code
                Action::make('qrCode')
                    ->label('Barcode')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn(Shortlink $record) => 'QR Code: ' . $record->pegawai_name)
                    ->modalDescription(fn(Shortlink $record) => 'Tautan: ' . $record->short_url)
                    ->modalContent(function (Shortlink $record) {
                        $rawSvg = (string) QrCode::format('svg')
                            ->size(220)
                            ->margin(2)
                            ->errorCorrection('H')
                            ->generate($record->short_url);

                        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($rawSvg);

                        return view('filament.components.qr-modal', [
                            'qrBase64'  => $qrBase64,
                            'shortlink' => $record,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                EditAction::make()
                    ->color('warning')
                    ->form([
                        TextInput::make('pegawai_name')
                            ->label('Nama Pegawai')
                            ->required(),

                        TextInput::make('destination_url')
                            ->label('URL Tujuan Asli')
                            ->url()
                            ->required(),

                        Toggle::make('is_capture_active')
                            ->label('Aktifkan Pengambilan Data')
                            ->live(),

                        CheckboxList::make('capture_fields')
                            ->label('Pilihan Data')
                            ->options([
                                'nama'     => '👤 Nama Lengkap',
                                'whatsapp' => '📱 Nomor WhatsApp',
                                'email'    => '✉️ Alamat Email',
                            ])
                            ->visible(fn(callable $get) => $get('is_capture_active')),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),

                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}

/**
 * =========================================================
 * WIDGET: TABEL REKAP PENGUNJUNG (LEADS)
 * =========================================================
 */
class DaftarLeadsTable extends TableWidget
{
    protected static ?string $heading = '📥 Data Pengunjung / Kontak Masuk';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshShortlinkTables' => '$refresh'];

    public function table(Table $table): Table
    {
        // 🔒 Superadmin (admin) melihat semua leads, adminshortlink hanya melihat data leads dari shortlink miliknya
        $query = ShortlinkLead::query()->with('shortlink')->latest();

        if (Auth::user()?->role !== 'admin') {
            $query->whereHas('shortlink', function ($q) {
                $q->where('created_by', Auth::id());
            });
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('shortlink.pegawai_name')
                    ->label('Link Pegawai')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('indigo'),

                TextColumn::make('nama')
                    ->label('Nama Pengunjung')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->default('-'),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->url(fn(?string $state) => $state ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $state) : null, true)
                    ->color('success')
                    ->default('-'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->default('-'),

                TextColumn::make('ip_address')
                    ->label('IP / Perangkat')
                    ->color('gray')
                    ->size('xs'),

                TextColumn::make('created_at')
                    ->label('Waktu Akses')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Download Data (Excel)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(route('admin.shortlink.leads.export'))
                    ->openUrlInNewTab(false),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
