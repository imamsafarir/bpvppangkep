<?php

namespace App\Filament\Pages;

use App\Models\BeritaDanGaleri;
use BackedEnum;
use Illuminate\Support\Str;

/* ===========================
| FILAMENT CORE
=========================== */
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

/* ===========================
| SCHEMA & FORM
=========================== */
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;

/* ===========================
| FORM COMPONENTS
=========================== */
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;

/* ===========================
| TABLE & WIDGET
=========================== */
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

/**
 * =========================================================
 * PAGE: MANAGE BERITA & GALERI
 * =========================================================
 */
class ManageBeritaDanGaleri extends Page
{
    protected string $view = 'filament.pages.manage-berita-dan-galeri';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $title = 'Kelola Berita & Galeri';
    protected static ?string $navigationLabel = 'Berita & Galeri';

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
                    Select::make('jenis')
                        ->label('Pilih Tipe Data')
                        ->options([
                            'berita' => '📰 Artikel Berita',
                            'galeri' => '🖼️ Galeri Kegiatan',
                        ])
                        ->required()
                        ->live(),

                    /* ================= BERITA ================= */
                    TextInput::make('judul_berita')
                        ->label('Judul Berita')
                        ->required()
                        ->visible(fn(Get $get) => $get('jenis') === 'berita'),

                    TagsInput::make('tags')
                        ->label('Kategori / Tags')
                        ->visible(fn(Get $get) => $get('jenis') === 'berita'),

                    RichEditor::make('konten_berita')
                        ->label('Isi Berita')
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('berita/konten')
                        ->visible(fn(Get $get) => $get('jenis') === 'berita')
                        ->columnSpanFull(),

                    /* ================= GALERI ================= */
                    TextInput::make('keterangan_galeri')
                        ->label('Keterangan Foto')
                        ->required()
                        ->visible(fn(Get $get) => $get('jenis') === 'galeri'),

                    /* ================= FOTO ================= */
                    FileUpload::make('file_foto')
                        ->label(
                            fn(Get $get) =>
                            $get('jenis') === 'berita'
                                ? 'Foto Sampul Berita'
                                : 'Foto Kegiatan (Multiple)'
                        )
                        ->disk('public')
                        ->visibility('public')
                        ->image()
                        ->visible(fn(Get $get) => filled($get('jenis')))
                        ->multiple(fn(Get $get) => $get('jenis') === 'galeri')
                        ->directory(
                            fn(Get $get) =>
                            $get('jenis') === 'berita'
                                ? 'berita/sampul'
                                : 'galeri/foto'
                        )
                        // GANTI method getUploadedFileNameForStorageUsing di form utama menjadi seperti ini:
                        ->getUploadedFileNameForStorageUsing(function (Get $get, $file) {
                            $base = $get('jenis') === 'berita'
                                ? $get('judul_berita')
                                : $get('keterangan_galeri');

                            // 💡 SOLUSI: Ditambah string acak di belakang time() agar file multiple tidak saling tindih
                            return Str::slug($base ?? 'file')
                                . '-' . time()
                                . '-' . bin2hex(random_bytes(4))
                                . '.' . $file->getClientOriginalExtension();
                        }),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan & Publikasikan')
                                ->submit('save')
                                ->color('primary'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if ($data['jenis'] === 'berita' && is_string($data['file_foto'])) {
            $data['file_foto'] = [$data['file_foto']];
        }

        BeritaDanGaleri::create($data);

        $this->form->fill();
        $this->dispatch('refreshTables');

        Notification::make()
            ->success()
            ->title('Data berhasil diterbitkan')
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            DaftarBeritaTable::class,
            DaftarGaleriTable::class,
        ];
    }
}

/**
 * =========================================================
 * WIDGET: DAFTAR BERITA
 * =========================================================
 */
class DaftarBeritaTable extends TableWidget
{
    protected static ?string $heading = '📋 Daftar Berita';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshTables' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BeritaDanGaleri::query()
                    ->where('jenis', 'berita')
                    ->latest()
            )
            ->columns([
                ImageColumn::make('file_foto')
                    ->disk('public')
                    ->square()
                    ->state(fn($record) => is_array($record->file_foto) ? ($record->file_foto[0] ?? null) : $record->file_foto),
                TextColumn::make('judul_berita')->searchable(),
                TextColumn::make('created_at')->date('d M Y'),
            ])
            ->actions([
                // 💡 Menggunakan Table EditAction resmi + Pembersih Storage saat edit
                // GANTI bagian EditAction milik DaftarBeritaTable menjadi seperti ini:
                EditAction::make()
                    ->color('warning')
                    ->form([
                        TextInput::make('judul_berita')->label('Judul Berita')->required(),
                        TagsInput::make('tags')->label('Kategori / Tags'),
                        RichEditor::make('konten_berita')->label('Isi Berita')->columnSpanFull(),
                        FileUpload::make('file_foto')
                            ->label('Foto Sampul Berita')
                            ->disk('public')
                            ->image()
                            ->directory('berita/sampul')
                            ->getUploadedFileNameForStorageUsing(fn($file) => 'berita-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension()),
                    ])
                    // 💡 PERBAIKAN: Menggunakan using() untuk handle kustomisasi save di Table Action
                    ->using(function (\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model {
                        $oldFiles = is_array($record->file_foto) ? $record->file_foto : json_decode($record->file_foto, true) ?? [];
                        $newFiles = isset($data['file_foto']) ? (is_array($data['file_foto']) ? $data['file_foto'] : [$data['file_foto']]) : [];

                        // Bersihkan file lama dari storage jika diganti
                        foreach ((array)$oldFiles as $oldFile) {
                            if (!in_array($oldFile, $newFiles) && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldFile)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFile);
                            }
                        }

                        // Pastikan format penyimpanan file_foto tetap array konsisten
                        if (isset($data['file_foto']) && is_string($data['file_foto'])) {
                            $data['file_foto'] = [$data['file_foto']];
                        }

                        // Jalankan update data ke database
                        $record->update($data);

                        return $record;
                    }),
                // 💡 Menggunakan Table DeleteAction resmi + Pembersih Storage saat baris dihapus
                DeleteAction::make()
                    ->before(function ($record) {
                        $files = is_array($record->file_foto) ? $record->file_foto : json_decode($record->file_foto, true) ?? [];
                        foreach ((array)$files as $file) {
                            if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                            }
                        }
                    }),
            ]);
    }
}

/**
 * =========================================================
 * WIDGET: DAFTAR GALERI
 * =========================================================
 */
class DaftarGaleriTable extends TableWidget
{
    protected static ?string $heading = '📸 Daftar Galeri';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshTables' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BeritaDanGaleri::query()
                    ->where('jenis', 'galeri')
                    ->latest()
            )
            ->columns([
                ImageColumn::make('file_foto')
                    ->disk('public')
                    ->square()
                    ->state(fn($record) => is_array($record->file_foto) ? ($record->file_foto[0] ?? null) : $record->file_foto),
                TextColumn::make('keterangan_galeri')->searchable(),
                TextColumn::make('created_at')->date('d M Y'),
            ])
            ->actions([
                // 💡 Menggunakan Table EditAction resmi + Pembersih Storage Multiple Galeri
                // GANTI bagian EditAction milik DaftarBeritaTable menjadi seperti ini:
                EditAction::make()
                    ->color('warning')
                    ->form([
                        TextInput::make('judul_berita')->label('Judul Berita')->required(),
                        TagsInput::make('tags')->label('Kategori / Tags'),
                        RichEditor::make('konten_berita')->label('Isi Berita')->columnSpanFull(),
                        FileUpload::make('file_foto')
                            ->label('Foto Sampul Berita')
                            ->disk('public')
                            ->image()
                            // 💡 PERBAIKAN 1: Wajib di-set multiple karena datanya array, agar tombol browse tidak hilang
                            ->multiple()
                            // 💡 PILIHAN: Jika ini sampul berita dan CUMA BOLEH 1 gambar, aktifkan baris bawah ini:
                            // ->maxFiles(1)
                            ->directory('berita/sampul')
                            ->getUploadedFileNameForStorageUsing(fn($file) => 'berita-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension()),
                    ])
                    ->using(function (\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model {
                        $oldFiles = is_array($record->file_foto) ? $record->file_foto : json_decode($record->file_foto, true) ?? [];

                        // 💡 PERBAIKAN 2: Karena sudah multiple, $data['file_foto'] otomatis selalu berbentuk array
                        $newFiles = $data['file_foto'] ?? [];

                        // Bersihkan file lama dari storage jika dihapus/diganti saat edit
                        foreach ((array)$oldFiles as $oldFile) {
                            if (!in_array($oldFile, $newFiles) && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldFile)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFile);
                            }
                        }

                        // Jalankan update data ke database secara aman
                        $record->update($data);

                        return $record;
                    }),

                DeleteAction::make()
                    ->before(function ($record) {
                        $files = is_array($record->file_foto) ? $record->file_foto : json_decode($record->file_foto, true) ?? [];
                        foreach ((array)$files as $file) {
                            if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                            }
                        }
                    }),
            ]);
    }
}
