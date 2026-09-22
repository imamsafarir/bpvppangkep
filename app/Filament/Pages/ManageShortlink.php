<?php

namespace App\Filament\Pages;

use App\Models\Shortlink;
use App\Models\ShortlinkLead;
use BackedEnum;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/* ===========================
| FILAMENT CORE
=========================== */
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

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

/* ===========================
| TABLE & WIDGET
=========================== */
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

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
        return $table
            ->query(Shortlink::query()->withCount('leads')->latest())
            ->columns([
                TextColumn::make('pegawai_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

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
                    ->limit(35)
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
                    ->label('Total Data Masuk')
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
                    ->color('gray')
                    ->formatStateUsing(function ($state) {
                        if (empty($state) || !is_array($state)) return '-';
                        return implode(', ', array_map('ucfirst', $state));
                    }),
            ])
            ->actions([
                // Tombol Lihat / Download QR Code
                Action::make('qrCode')
                    ->label('Barcode')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn(Shortlink $record) => 'QR Code: ' . $record->pegawai_name)
                    ->modalDescription(fn(Shortlink $record) => 'Tautan: ' . $record->short_url)
                    ->modalContent(function (Shortlink $record) {
                        $svg = QrCode::size(240)->margin(2)->generate($record->short_url);
                        return view('filament.components.qr-modal', [
                            'svg'       => $svg,
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
        return $table
            ->query(ShortlinkLead::query()->with('shortlink')->latest())
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
            ->actions([
                DeleteAction::make(),
            ]);
    }
}
