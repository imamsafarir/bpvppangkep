<?php

namespace App\Modules\Website\Filament\Pages;

use App\Modules\Website\Models\PelayananPublik;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

// Import komponen Form yang dibutuhkan
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Repeater;

// Namespace Section resmi milik Filament
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;
use BackedEnum;

// 💡 IMPORT BARU: Kebutuhan penamaan berkas & manajemen disk storage
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Schema $form
 */
class ManagePelayananPublik extends Page
{
    protected string $view = 'website::filament.manage-pelayanan-publik';
    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $title = 'Kelola Pelayanan Publik';
    protected static ?string $navigationLabel = 'Pelayanan Publik';


    public static function getNavigationGroup(): ?string
    {
        return 'Website';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
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
                ->modalDescription('Proses ini akan memeriksa foto alur pelayanan, file maklumat, standar pelayanan, dan attachment teks penjelasan serta memindahkannya ke folder website/pelayanan/ yang rapi serta mengompres ke AVIF. Lanjutkan?')
                ->modalSubmitActionLabel('Ya, Sinkronisasi Sekarang')
                ->action(function () {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('website:migrate-images');
                        $this->form->fill($this->getRecord()?->attributesToArray());

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Sinkronisasi Selesai')
                            ->body('Seluruh dokumen pelayanan dan attachment berhasil disinkronkan ke AVIF.')
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

                    // SECTION 1: Maklumat Pelayanan
                    Section::make('1. Maklumat Pelayanan')
                        ->description('Kelola dokumen maklumat pelayanan resmi balai (bisa lebih dari 1).')
                        ->schema([
                            Repeater::make('maklumat_pelayanan')
                                ->label('Daftar Maklumat Pelayanan')
                                ->schema([
                                    TextInput::make('judul_maklumat')
                                        ->label('Judul / Nama Maklumat')
                                        ->placeholder('Contoh: Maklumat Pelayanan Tahun 2026')
                                        ->live(onBlur: true), // 💡 Memicu pembacaan state judul secara live
                                    FileUpload::make('file_maklumat')
                                        ->label('File Dokumen (PDF) / Foto')
                                        ->disk('public')
                                        ->directory('website/pelayanan/maklumat')
                                        ->maxSize(5120)
                                        // 💡 PENAMAAN RAPI BERDASARKAN JUDUL MAKLUMAT
                                        ->getUploadedFileNameForStorageUsing(
                                            fn(Get $get, TemporaryUploadedFile $file): string => Str::slug($get('judul_maklumat') ?? 'maklumat') . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension()
                                        ),
                                    RichEditor::make('keterangan_maklumat')
                                        ->label('Keterangan Tambahan')
                                        ->fileAttachmentsDisk('public')
                                        ->fileAttachmentsDirectory('website/pelayanan/maklumat/konten')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->createItemButtonLabel('Tambah Maklumat Baru')
                                ->collapsible(),
                        ]),

                    // SECTION 2: Standar Pelayanan
                    Section::make('2. Standar Pelayanan Publik')
                        ->description('Kelola standar acuan komponen pelayanan acuan masyarakat (bisa lebih dari 1).')
                        ->schema([
                            Repeater::make('standar_pelayanan')
                                ->label('Daftar Standar Pelayanan Publik')
                                ->schema([
                                    TextInput::make('judul_standar')
                                        ->label('Nama Standar Pelayanan / Jenis Layanan')
                                        ->placeholder('Contoh: Standar Pelayanan Pelatihan Berbasis Kompetensi')
                                        ->live(onBlur: true),
                                    FileUpload::make('file_standar')
                                        ->label('File Dokumen (PDF) / Foto')
                                        ->disk('public')
                                        ->directory('website/pelayanan/standar')
                                        ->maxSize(10240)
                                        // 💡 PENAMAAN RAPI BERDASARKAN JUDUL STANDAR
                                        ->getUploadedFileNameForStorageUsing(
                                            fn(Get $get, TemporaryUploadedFile $file): string => Str::slug($get('judul_standar') ?? 'standar') . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension()
                                        ),
                                    RichEditor::make('keterangan_standar')
                                        ->label('Keterangan / Komponen Standar')
                                        ->fileAttachmentsDisk('public')
                                        ->fileAttachmentsDirectory('website/pelayanan/standar/konten')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->createItemButtonLabel('Tambah Standar Pelayanan Baru')
                                ->collapsible(),
                        ]),

                    // SECTION 3: Alur Pelayanan
                    Section::make('3. Alur Pelayanan Publik')
                        ->description('Tambahkan bagan alur pelayanan resmi balai. Anda bisa menambahkan lebih dari 1 langkah alur.')
                        ->schema([
                            Repeater::make('alur_pelayanan')
                                ->label('Daftar Dokumen / Langkah Alur Pelayanan')
                                ->schema([
                                    TextInput::make('judul_alur')
                                        ->label('Judul Langkah / Alur')
                                        ->placeholder('Contoh: Alur Pendaftaran Pelatihan')
                                        ->live(onBlur: true),
                                    FileUpload::make('foto_alur')
                                        ->label('Bagan / Foto Alur')
                                        ->image()
                                        ->disk('public')
                                        ->directory('website/pelayanan/alur')
                                        ->maxSize(3072)
                                        // Penamaan rapi berdasarkan judul alur, output .avif
                                        ->getUploadedFileNameForStorageUsing(
                                            fn(Get $get, TemporaryUploadedFile $file): string => Str::slug($get('judul_alur') ?? 'alur') . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.avif'
                                        ),
                                    RichEditor::make('deskripsi_alur')
                                        ->label('Keterangan Tambahan')
                                        ->fileAttachmentsDisk('public')
                                        ->fileAttachmentsDirectory('website/pelayanan/alur/konten')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->createItemButtonLabel('Tambah Alur / Bagan Baru')
                                ->grid(2),
                        ]),

                    // Section 4, 5, 6: Tautan & Live Preview Survey
                    Section::make('Tautan & Live Preview Survey')
                        ->description('Masukkan link Google Form (GForm). Form akan otomatis muncul di bawah inputan secara interaktif.')
                        ->schema([
                            TextInput::make('survey_kepuasan_masyarakat')
                                ->label('4. Link Google Form: Survey Kepuasan Masyarakat')
                                ->url()
                                ->placeholder('https://docs.google.com/forms/d/e/.../viewform')
                                ->live(),

                            ViewField::make('embed_skm')
                                ->view('website::filament.gform-embed')
                                ->viewData([
                                    'url' => $this->data['survey_kepuasan_masyarakat'] ?? null,
                                    'label' => 'Live Preview Survey Kepuasan Masyarakat'
                                ]),

                            TextInput::make('survey_kebutuhan_pelatihan')
                                ->label('5. Link Google Form: Survey Kebutuhan Pelatihan')
                                ->url()
                                ->placeholder('https://docs.google.com/forms/d/e/.../viewform')
                                ->live(),

                            ViewField::make('embed_akp')
                                ->view('website::filament.gform-embed')
                                ->viewData([
                                    'url' => $this->data['survey_kebutuhan_pelatihan'] ?? null,
                                    'label' => 'Live Preview Survey Kebutuhan Pelatihan'
                                ]),

                            TextInput::make('survey_kebekerjaan')
                                ->label('6. Link Google Form: Survey Kebekerjaan (Tracer Study)')
                                ->url()
                                ->placeholder('https://docs.google.com/forms/d/e/.../viewform')
                                ->live(),

                            ViewField::make('embed_tracer')
                                ->view('website::filament.gform-embed')
                                ->viewData([
                                    'url' => $this->data['survey_kebekerjaan'] ?? null,
                                    'label' => 'Live Preview Survey Kebekerjaan'
                                ]),
                        ]),

                    // Section 7: Hasil Indeks Kepuasan
                    Section::make('7. Indeks Kepuasan Masyarakat (IKM)')
                        ->description('Tampilkan laporan statistik nilai atau ringkasan pencapaian IKM Balai.')
                        ->schema([
                            RichEditor::make('indeks_kepuasan_masyarakat')
                                ->label('Laporan Nilai / Indeks Kepuasan')
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('website/pelayanan/ikm/konten'),
                        ]),

                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan Perubahan Pelayanan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    /**
     * FUNGSI SIMPAN: Proses kompilasi data & pembersihan mutlak berkas sampah di storage
     */
    public function save(): void
    {
        $validatedData = $this->form->getState();
        $record = $this->getRecord();

        if ($record) {
            // 🟢 DETEKSI OTOMATIS BERKAS REPEATER YANG DIHAPUS / DIGANTI BARU
            $repeaterConfig = [
                'maklumat_pelayanan' => ['file_maklumat'],
                'standar_pelayanan'  => ['file_standar'],
                'alur_pelayanan'     => ['foto_alur'],
            ];

            $filesToDelete = [];

            foreach ($repeaterConfig as $dbField => $fileKeys) {
                // Ekstraksi data lama dari database
                $oldJson = $record->$dbField;
                $oldItems = is_string($oldJson) ? json_decode($oldJson, true) : ($oldJson ?? []);
                $oldItems = is_array($oldItems) ? $oldItems : [];

                // Ambil data baru hasil inputan form saat ini
                $newItems = $validatedData[$dbField] ?? [];
                $newItems = is_array($newItems) ? $newItems : [];

                // Himpun seluruh berkas baru untuk dijadikan pembanding whitelist
                $newFilePaths = [];
                foreach ($newItems as $newItem) {
                    foreach ($fileKeys as $key) {
                        if (! empty($newItem[$key])) {
                            $newFilePaths[] = $newItem[$key];
                        }
                    }
                }

                // Jika berkas lama tidak ada di daftar form baru, bersihkan
                foreach ($oldItems as $oldItem) {
                    foreach ($fileKeys as $key) {
                        if (! empty($oldItem[$key]) && ! in_array($oldItem[$key], $newFilePaths)) {
                            $filesToDelete[] = $oldItem[$key];
                        }
                    }
                }
            }

            // Eksekusi penghapusan fisik berkas dari disk storage public
            foreach ($filesToDelete as $filePath) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        // Simpan baris data ke database
        if (! $record) {
            $record = new PelayananPublik();
            $record->id = 1;
        }

        $record->fill($validatedData);
        $record->save();

        Notification::make()
            ->success()
            ->title('Data Pelayanan Publik & Link Survey berhasil diperbarui')
            ->send();
    }

    public function getRecord(): ?PelayananPublik
    {
        return PelayananPublik::query()->first();
    }
}
