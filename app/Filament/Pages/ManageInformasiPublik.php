<?php

namespace App\Filament\Pages;

use App\Models\InformasiPublik;
use BackedEnum;
use Illuminate\Support\Str;

/* ================= CORE ================= */
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/* ================= SCHEMA ================= */
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;

/* ================= FORM ================= */
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

/* ================= TABLE ================= */
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

/**
 * =========================================================
 * PAGE: MANAGE INFORMASI PUBLIK
 * =========================================================
 */
class ManageInformasiPublik extends Page
{
    protected string $view = 'filament.pages.manage-informasi-publik';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $title = 'Kelola Informasi Publik';
    protected static ?string $navigationLabel = 'Informasi Publik';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Select::make('kategori')
                        ->label('Kategori Informasi Publik')
                        ->options([
                            'berkala' => '1. Informasi Berkala',
                            'serta_merta' => '2. Informasi Serta Merta',
                            'setiap_saat' => '3. Informasi Tersedia Setiap Saat',
                        ])
                        ->required()
                        ->live(),

                    TextInput::make('nama_dokumen')
                        ->label('Nama Dokumen')
                        ->required()
                        ->maxLength(255)
                        ->visible(fn(Get $get) => filled($get('kategori'))),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi Singkat')
                        ->rows(2)
                        ->visible(fn(Get $get) => filled($get('kategori'))),

                    FileUpload::make('file_path')
                        ->label('Upload File Dokumen')
                        ->required()
                        ->maxSize(10240)
                        ->visible(fn(Get $get) => filled($get('kategori')))
                        ->disk('public')
                        ->visibility('public')
                        ->directory(fn(Get $get) => 'informasi-publik/' . $get('kategori'))
                        ->getUploadedFileNameForStorageUsing(function (Get $get, $file) {
                            return Str::slug($get('nama_dokumen'))
                                . '-' . time()
                                . '.' . $file->getClientOriginalExtension();
                        }),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan & Terbitkan')
                                ->submit('save')
                                ->color('primary'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        InformasiPublik::create($this->form->getState());

        $this->form->fill();
        $this->dispatch('refreshTables');

        Notification::make()
            ->success()
            ->title('Informasi publik berhasil ditambahkan')
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            InformasiBerkalaTable::class,
            InformasiSertaMertaTable::class,
            InformasiSetiapSaatTable::class,
        ];
    }
}

/**
 * =========================================================
 * BASE TABLE (ABSTRAK)
 * =========================================================
 */
abstract class BaseInformasiPublikTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $kategori = null;

    protected $listeners = ['refreshTables' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InformasiPublik::query()
                    ->where('kategori', static::$kategori)
                    ->latest()
            )
            ->columns([
                TextColumn::make('nama_dokumen')->searchable(),
                TextColumn::make('created_at')->label('Tanggal Upload')->date('d M Y'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat File')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->form([
                        TextInput::make('nama_dokumen')->required(),
                        Textarea::make('deskripsi')->rows(2),
                        FileUpload::make('file_path')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('informasi-publik/' . static::$kategori)
                            ->maxSize(10240)
                            ->getUploadedFileNameForStorageUsing(function (Get $get, $file) {
                                return Str::slug($get('nama_dokumen'))
                                    . '-' . time()
                                    . '.' . $file->getClientOriginalExtension();
                            }),
                    ]),

                DeleteAction::make(),
            ]);
    }
}

/**
 * =========================================================
 * TABEL: BERKALA
 * =========================================================
 */
class InformasiBerkalaTable extends BaseInformasiPublikTable
{
    protected static ?string $heading = 'Daftar Informasi Berkala';
    protected static ?string $kategori = 'berkala';
}

/**
 * =========================================================
 * TABEL: SERTA MERTA
 * =========================================================
 */
class InformasiSertaMertaTable extends BaseInformasiPublikTable
{
    protected static ?string $heading = 'Daftar Informasi Serta Merta';
    protected static ?string $kategori = 'serta_merta';
}

/**
 * =========================================================
 * TABEL: SETIAP SAAT
 * =========================================================
 */
class InformasiSetiapSaatTable extends BaseInformasiPublikTable
{
    protected static ?string $heading = 'Daftar Informasi Tersedia Setiap Saat';
    protected static ?string $kategori = 'setiap_saat';
}
