<?php

namespace App\Filament\Pages;

use App\Models\WebsiteSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

// Import komponen Form yang dibutuhkan
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;

// Import komponen Section resmi
use Filament\Schemas\Components\Section;
use BackedEnum;

// 💡 IMPORT BARU: Untuk urusan kerapihan nama file & penghapusan storage
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Schema $form
 */
class ManageWebsiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-website-settings';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $title = 'Pengaturan Website';
    protected static ?string $navigationLabel = 'Pengaturan Website';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([

                    // SECTION: Identitas Umum Website
                    Section::make('🌐 Identitas Utama Website')
                        ->description('Atur nama website, ikon logo, favicon sistem, beserta banner slider utama.')
                        ->schema([
                            TextInput::make('website_name')
                                ->label('Nama Website / Sistem')
                                ->required()
                                ->columnSpanFull(),

                            FileUpload::make('logo_path')
                                ->label('Logo Utama Website (Navbar)')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('settings/branding')
                                // 💡 KUNCI PENAMAAN RAPI
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string => 'logo_' . time() . '.' . $file->getClientOriginalExtension()
                                )
                                ->maxSize(2048),

                            FileUpload::make('favicon_path')
                                ->label('Favicon Tab Browser (.ico / .png)')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('settings/branding')
                                // 💡 KUNCI PENAMAAN RAPI
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string => 'favicon_' . time() . '.' . $file->getClientOriginalExtension()
                                )
                                ->maxSize(512),

                            // MULTIPLE FILE UPLOAD UNTUK COVER SLIDER
                            FileUpload::make('sliders')
                                ->label('Cover Slider Banner (Bisa Pilih Lebih Dari 1 Gambar)')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('settings/sliders')
                                // 💡 KUNCI PENAMAAN RAPI MULTIPLE (ditambah string acak agar tidak bentrok saat upload barengan)
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string => 'slider_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension()
                                )
                                ->maxSize(5120)
                                ->columnSpanFull()
                                ->helperText('Unggah satu atau beberapa gambar cover untuk slider beranda (Maks. 5MB per file)'),
                        ])
                        ->columns(2),

                    // Section: Kontak & Informasi Resmi Kantor
                    Section::make('📞 Informasi Kontak Resmi Kantor')
                        ->description('Masukkan identitas kontak resmi BPVP Pangkep untuk ditampilkan pada bagian footer.')
                        ->schema([
                            TextInput::make('email')
                                ->label('Alamat Email Resmi')
                                ->email(),
                            TextInput::make('whatsapp_number')
                                ->label('Nomor WhatsApp')
                                ->maxLength(20),
                            TextInput::make('phone_number')
                                ->label('Nomor Telepon Kantor')
                                ->maxLength(30),
                            Textarea::make('address')
                                ->label('Alamat Fisik Lengkap')
                                ->rows(2)
                                ->columnSpanFull(),
                            Textarea::make('google_maps_embed')
                                ->label('Link Iframe Google Maps Embed')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->columns(3),

                    // Section: Alamat Sosial Media Balai
                    Section::make('🔗 Alamat Sosial Media Resmi')
                        ->schema([
                            TextInput::make('facebook_url')->label('Facebook URL')->url(),
                            TextInput::make('instagram_url')->label('Instagram URL')->url(),
                            TextInput::make('youtube_url')->label('YouTube Channel URL')->url(),
                            TextInput::make('tiktok_url')->label('TikTok URL')->url(),
                        ])
                        ->columns(2),

                    // Section: Popup Iklan / Banner Pengumuman
                    Section::make('🖼️ Pop-Up Banner Informasi / Iklan')
                        ->schema([
                            Toggle::make('is_popup_active')->label('Aktifkan Pop-Up Iklan')->inline(false),
                            FileUpload::make('popup_image_path')
                                ->label('Gambar Banner Pop-Up')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('settings/popup')
                                // 💡 KUNCI PENAMAAN RAPI
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string => 'popup_' . time() . '.' . $file->getClientOriginalExtension()
                                )
                                ->maxSize(2048),
                            TextInput::make('popup_redirect_url')->label('Link Tujuan Pengalihan (Optional)')->url()->columnSpanFull(),
                        ])
                        ->columns(2),

                    // Section: Teks Berjalan (Running Text)
                    Section::make('📢 Teks Berjalan (Running Text) Pengumuman')
                        ->schema([
                            Toggle::make('is_running_text_active')->label('Aktifkan Teks Berjalan')->inline(false),
                            Textarea::make('running_text_content')->label('Isi Teks Pengumuman')->rows(3),
                        ]),

                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan Semua Pengaturan')
                                ->submit('save')
                                ->color('primary'),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    /**
     * FUNGSI SIMPAN: Menyimpan data & membersihkan file sampah di storage
     */
    public function save(): void
    {
        $validatedData = $this->form->getState();
        $record = $this->getRecord();

        // 🟢 PROSES DETEKSI & PENGHAPUSAN FILE YANG DIGANTI / DIHAPUS
        $filesToDelete = [];

        if ($record) {
            // 1. Cek perubahan pada file tunggal
            foreach (['logo_path', 'favicon_path', 'popup_image_path'] as $field) {
                $oldValue = $record->$field;
                $newValue = $validatedData[$field] ?? null;

                // Jika dulu ada file, tapi sekarang diganti baru atau dikosongkan (dihapus)
                if ($oldValue && $oldValue !== $newValue) {
                    $filesToDelete[] = $oldValue;
                }
            }

            // 2. Cek perubahan pada multiple file (sliders)
            $oldSliders = is_string($record->sliders) ? json_decode($record->sliders, true) : ($record->sliders ?? []);
            $newSliders = $validatedData['sliders'] ?? [];

            if (is_array($oldSliders)) {
                foreach ($oldSliders as $oldSlider) {
                    // Jika path gambar lama tidak ditemukan lagi di array baru, artinya dihapus oleh admin
                    if (! in_array($oldSlider, $newSliders)) {
                        $filesToDelete[] = $oldSlider;
                    }
                }
            }
        }

        // 🟢 EKSEKUSI PENGHAPUSAN FISIK DARI DISK STORAGE
        foreach ($filesToDelete as $filePath) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        // Jalankan penyimpanan bodi model ke database
        if (! $record) {
            $record = new WebsiteSetting();
            $record->id = 1;
        }

        $record->fill($validatedData);
        $record->save();

        Notification::make()
            ->success()
            ->title('Pengaturan website berhasil disimpan')
            ->send();
    }

    public function getRecord(): ?WebsiteSetting
    {
        return WebsiteSetting::query()->first();
    }
}
