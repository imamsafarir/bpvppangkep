<?php

namespace App\Modules\Website\Filament\Pages;

use App\Modules\Website\Models\Informasi;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

// Import komponen form Filament yang dibutuhkan
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use BackedEnum;

// 💡 IMPORT BARU: Untuk pengelolaan penamaan rapi & penghapusan otomatis di storage
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Schema $form
 */
class ManageInformasi extends Page
{
    protected string $view = 'website::filament.manage-informasi';
    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $title = 'Kelola Informasi';
    protected static ?string $navigationLabel = 'Kelola Informasi';


    public static function getNavigationGroup(): ?string
    {
        return 'Website';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isWebsite() || auth()->user()?->isStaff();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('migrate_images')
                ->label('🔄 Sinkronisasi & Kompres Gambar')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Gambar & Dokumen Website')
                ->modalDescription('Proses ini akan memeriksa foto kejuruan, sarana workshop, fasilitas, alumni, testimoni, kerjasama, serta attachment konten lainnya dan memindahkannya ke folder website/informasi/ yang rapi serta mengompres ke AVIF. Lanjutkan?')
                ->modalSubmitActionLabel('Ya, Sinkronisasi Sekarang')
                ->action(function () {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('website:migrate-images');
                        $this->form->fill($this->getRecord()?->attributesToArray());

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Sinkronisasi Selesai')
                            ->body('Seluruh gambar informasi dan attachment berhasil disinkronkan ke AVIF.')
                            ->send();
                    } catch (\Throwable $e) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Sinkronisasi Gagal')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('Informasi Tabs')
                        ->tabs([

                            // Tab 1: Kejuruan
                            Tab::make('Kejuruan')
                                ->schema([
                                    Repeater::make('kejuruan')
                                        ->label('Daftar Kejuruan Resmi')
                                        ->schema([
                                            TextInput::make('nama_kejuruan')->label('Nama Kejuruan'),
                                            FileUpload::make('foto_kejuruan')
                                                ->label('Foto / Gambar Kejuruan')
                                                ->disk('public')
                                                ->image()
                                                ->directory('website/informasi/kejuruan')
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn(TemporaryUploadedFile $file): string => 'kejuruan_' . time() . '_' . bin2hex(random_bytes(4)) . '.avif'
                                                )
                                                ->maxSize(2048),
                                            RichEditor::make('deskripsi_kejuruan')
                                                ->label('Deskripsi / Detail Kejuruan')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/kejuruan/konten')
                                                ->columnSpanFull(),
                                        ])->columns(2)->createItemButtonLabel('Tambah Kejuruan Baru'),
                                ]),

                            // Tab 2: Gedung & Fasilitas
                            Tab::make('Gedung & Fasilitas')
                                ->schema([
                                    Repeater::make('gedung_fasilitas')
                                        ->label('Daftar Gedung & Fasilitas Utama')
                                        ->schema([
                                            TextInput::make('nama_fasilitas')->label('Nama Gedung / Fasilitas'),
                                            FileUpload::make('foto_fasilitas')
                                                ->label('Foto Fasilitas')
                                                ->disk('public')
                                                ->image()
                                                ->directory('website/informasi/fasilitas')
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn(TemporaryUploadedFile $file): string => 'fasilitas_' . time() . '_' . bin2hex(random_bytes(4)) . '.avif'
                                                )
                                                ->maxSize(3072),
                                            RichEditor::make('deskripsi_fasilitas')
                                                ->label('Keterangan Gedung / Sarana')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/fasilitas/konten')
                                                ->columnSpanFull(),
                                        ])->columns(2)->createItemButtonLabel('Tambah Fasilitas Baru'),
                                ]),

                            // Tab 3: Ruang Kelas & Workshop
                            Tab::make('Ruang Kelas & Workshop')
                                ->schema([
                                    Repeater::make('kelas_workshop')
                                        ->label('Daftar Ruang Kelas & Sarana Workshop')
                                        ->schema([
                                            TextInput::make('nama_ruangan')->label('Nama Kelas / Workshop'),
                                            FileUpload::make('foto_ruangan')
                                                ->label('Foto Kondisi Ruangan')
                                                ->disk('public')
                                                ->image()
                                                ->directory('website/informasi/workshop')
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn(TemporaryUploadedFile $file): string => 'workshop_' . time() . '_' . bin2hex(random_bytes(4)) . '.avif'
                                                )
                                                ->maxSize(3072),
                                            RichEditor::make('deskripsi_ruangan')
                                                ->label('Detail Fasilitas Ruangan')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/workshop/konten')
                                                ->columnSpanFull(),
                                        ])->columns(2)->createItemButtonLabel('Tambah Ruang / Workshop Baru'),
                                ]),

                            // Tab 4: Alumni
                            Tab::make('Alumni')
                                ->schema([
                                    Repeater::make('alumni')
                                        ->label('Informasi Catatan Kinerja Alumni')
                                        ->schema([
                                            TextInput::make('tahun_angkatan')->label('Tahun Kelulusan / Angkatan')->placeholder('Contoh: Angkatan 2025'),
                                            FileUpload::make('foto_kegiatan_alumni')
                                                ->label('Foto Dokumentasi Alumni')
                                                ->disk('public')
                                                ->image()
                                                ->directory('website/informasi/alumni')
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn(TemporaryUploadedFile $file): string => 'alumni_' . time() . '_' . bin2hex(random_bytes(4)) . '.avif'
                                                )
                                                ->maxSize(2048),
                                            RichEditor::make('catatan_alumni')
                                                ->label('Detail Informasi / Karir Alumni')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/alumni/konten')
                                                ->columnSpanFull(),
                                        ])->columns(2)->createItemButtonLabel('Tambah Catatan Alumni Baru'),
                                ]),

                            // Tab 5: Testimoni
                            Tab::make('Testimoni Alumni')
                                ->schema([
                                    Repeater::make('testimoni')
                                        ->label('Daftar Testimoni Alumni')
                                        ->schema([
                                            TextInput::make('nama_alumni')->label('Nama Lengkap Alumni'),
                                            TextInput::make('pekerjaan')->label('Bekerja di / Wirausaha'),
                                            RichEditor::make('isi_testimoni')
                                                ->label('Kalimat Testimoni')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/testimoni/konten')
                                                ->columnSpanFull(),
                                            FileUpload::make('foto_alumni')
                                                ->label('Foto Alumni')
                                                ->disk('public')
                                                ->image()
                                                ->directory('website/informasi/testimoni')
                                                ->imageCropAspectRatio('1:1')
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn(TemporaryUploadedFile $file): string => 'testimoni_' . time() . '_' . bin2hex(random_bytes(4)) . '.avif'
                                                )
                                                ->maxSize(1024),
                                        ])->columns(2)->createItemButtonLabel('Tambah Testimoni Baru'),
                                ]),

                            // Tab 6: Kerjasama
                            Tab::make('Kerjasama')
                                ->schema([
                                    Repeater::make('kerjasama')
                                        ->label('Daftar Kerjasama Instansi / Perusahaan')
                                        ->schema([
                                            FileUpload::make('logo')
                                                ->label('Logo Instansi / Perusahaan')
                                                ->disk('public')
                                                ->image()
                                                ->directory('website/informasi/kerjasama')
                                                ->imagePreviewHeight('100')
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn(TemporaryUploadedFile $file): string => 'kerjasama_' . time() . '_' . bin2hex(random_bytes(4)) . '.avif'
                                                )
                                                ->maxSize(1024),
                                            TextInput::make('nama_instansi')
                                                ->label('Nama Instansi / Perusahaan')
                                                ->maxLength(255),
                                            RichEditor::make('bentuk_kerjasama')
                                                ->label('Bentuk / Deskripsi Kerjasama')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/kerjasama/konten')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->createItemButtonLabel('Tambah Kerjasama Baru'),
                                ]),

                            // Tab 7: FAQ
                            Tab::make('FAQ')
                                ->schema([
                                    Repeater::make('faq')
                                        ->label('Daftar Pertanyaan yang Sering Diajukan (FAQ)')
                                        ->schema([
                                            TextInput::make('pertanyaan')
                                                ->label('Pertanyaan / Judul Masalah')
                                                ->maxLength(255),
                                            RichEditor::make('jawaban')
                                                ->label('Jawaban / Penjelasan Lengkap')
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory('website/informasi/faq/konten')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(1)
                                        ->createItemButtonLabel('Tambah FAQ Baru'),
                                ]),

                        ])->columnSpanFull(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan Perubahan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    /**
     * FUNGSI SIMPAN: Sinkronisasi data JSON & Penghapusan fisik berkas sampah di Storage
     */
    public function save(): void
    {
        $validatedData = $this->form->getState();
        $record = $this->getRecord();

        if ($record) {
            // 🟢 PENGHAPUSAN OTOMATIS BERKAS REPEATER YANG DIHAPUS / DIGANTI
            // Daftarkan pemetaan key tabel json beserta key field upload di dalamnya
            $repeaterConfig = [
                'kejuruan'         => ['foto_kejuruan'],
                'gedung_fasilitas' => ['foto_fasilitas'],
                'kelas_workshop'   => ['foto_ruangan'],
                'alumni'           => ['foto_kegiatan_alumni'],
                'testimoni'        => ['foto_alumni'],
                'kerjasama'        => ['logo'],
            ];

            $filesToDelete = [];

            foreach ($repeaterConfig as $dbField => $fileKeys) {
                // Ekstraksi data lama dari database
                $oldJson = $record->$dbField;
                $oldItems = is_string($oldJson) ? json_decode($oldJson, true) : ($oldJson ?? []);
                $oldItems = is_array($oldItems) ? $oldItems : [];

                // Ambil data baru hasil input form
                $newItems = $validatedData[$dbField] ?? [];
                $newItems = is_array($newItems) ? $newItems : [];

                // Kumpulkan seluruh path file baru untuk dijadikan pembanding
                $newFilePaths = [];
                foreach ($newItems as $newItem) {
                    foreach ($fileKeys as $key) {
                        if (! empty($newItem[$key])) {
                            $newFilePaths[] = $newItem[$key];
                        }
                    }
                }

                // Bandingkan berkas lama: Jika tidak ada di daftar form baru, masukkan daftar hapus
                foreach ($oldItems as $oldItem) {
                    foreach ($fileKeys as $key) {
                        if (! empty($oldItem[$key]) && ! in_array($oldItem[$key], $newFilePaths)) {
                            $filesToDelete[] = $oldItem[$key];
                        }
                    }
                }
            }

            // Eksekusi pembersihan file fisik dari storage disk
            foreach ($filesToDelete as $filePath) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        // Proses penyimpanan standar model Informasi
        if (! $record) {
            $record = new Informasi();
            $record->id = 1;
        }

        $record->fill($validatedData);
        $record->save();

        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('Seluruh Data Informasi berhasil diperbarui')
            ->send();
    }

    public function getRecord(): ?Informasi
    {
        return Informasi::query()->first();
    }
}
