<?php

namespace App\Modules\Website\Filament\Pages;

use App\Modules\Website\Models\Profil;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

// Import komponen form Filament yang dibutuhkan secara akurat
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section; // <-- Ubah ke Schemas
use Filament\Schemas\Components\Grid;
use BackedEnum;

/**
 * @property-read Schema $form
 */
class ManageProfil extends Page
{
    protected string $view = 'website::filament.manage-profil';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $title = 'Profil Balai';
    protected static ?string $navigationLabel = 'Profil Balai';


    public static function getNavigationGroup(): ?string
    {
        return 'Website';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'staff']);
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        // Isi form dengan atribut data dari database jika record sudah ada
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('Profil Tabs')
                        ->tabs([

                            // TAB 1: SAMBUTAN & TENTANG KAMU (Sudah Diperbaiki Strukturnya)
                            Tab::make('Sambutan & Tentang Kami')
                                ->schema([

                                    // Dibungkus Section khusus Kepala Balai agar terpisah dan rapi secara visual
                                    Section::make('👨‍💼 Identitas Kepala Balai')
                                        ->description('Unggah foto resmi beserta nama lengkap dan nomor NIP Kepala Balai.')
                                        ->schema([
                                            FileUpload::make('chief_photo_path')
                                                ->label('Foto Resmi Kepala Balai')
                                                ->image()
                                                ->disk('public')
                                                ->directory('profil/chief')
                                                ->imageCropAspectRatio('3:4')
                                                ->maxSize(2048)
                                                ->columnSpan(['md' => 1]), // Foto memakan 1 kolom di desktop

                                            Grid::make(1)
                                                ->schema([
                                                    TextInput::make('chief_name')
                                                        ->label('Nama Lengkap & Gelar Kepala Balai')
                                                        ->placeholder('Contoh: Nama Kepala, S.T., M.M.')
                                                        ->required(),

                                                    TextInput::make('chief_nip')
                                                        ->label('NIP Kepala Balai')
                                                        ->placeholder('Contoh: 19850617...'),
                                                ])
                                                ->columnSpan(['md' => 2]), // Form input teks memakan 2 kolom berdampingan
                                        ])
                                        ->columns([
                                            'sm' => 1,
                                            'md' => 3,
                                        ]),

                                    // Bagian Editor Teks Utama di bawah kartu identitas
                                    RichEditor::make('sambutan_kepala')
                                        ->label('Sambutan Kepala Balai')
                                        ->fileAttachmentsDirectory('profil/sambutan')
                                        ->columnSpanFull(),

                                    RichEditor::make('tentang_kami')
                                        ->label('Tentang Kami')
                                        ->columnSpanFull(),
                                ]),

                            // TAB 2: VISI MISI
                            Tab::make('Visi, Misi & Tupoksi')
                                ->schema([
                                    RichEditor::make('visi_misi')
                                        ->label('Visi & Misi'),
                                    RichEditor::make('tugas_fungsi')
                                        ->label('Tugas dan Fungsi'),
                                ]),

                            // TAB 3: PPID
                            Tab::make('PPID')
                                ->schema([
                                    RichEditor::make('ppid')
                                        ->label('Konten PPID'),
                                ]),

                            // TAB 4: STRUKTUR & PEJABAT
                            Tab::make('Struktur & Pejabat')
                                ->schema([
                                    FileUpload::make('struktur_organisasi')
                                        ->label('Foto Struktur Organisasi')
                                        ->image()
                                        ->disk('public')
                                        ->directory('profil/struktur')
                                        ->maxSize(2048),

                                    Repeater::make('pejabat_struktural')
                                        ->label('Profil Pejabat Struktural')
                                        ->schema([
                                            TextInput::make('nama')
                                                ->label('Nama Lengkap & Gelar')
                                                ->required(),

                                            TextInput::make('jabatan')
                                                ->label('Jabatan Saat Ini')
                                                ->required(),

                                            FileUpload::make('foto')
                                                ->label('Foto Pas Pejabat')
                                                ->image()
                                                ->disk('public')
                                                ->directory('profil/pejabat')
                                                ->imageCropAspectRatio('3:4')
                                                ->maxSize(1024),

                                            // TAMBAHKAN REPEATER SUB-LEVEL UNTUK RIWAYAT JABATAN
                                            Repeater::make('riwayat_jabatan')
                                                ->label('📋 Rekam Jejak / Riwayat Jabatan')
                                                ->schema([
                                                    TextInput::make('tahun')
                                                        ->label('Periode / Tahun')
                                                        ->placeholder('Contoh: 2022 - 2024 atau 2025')
                                                        ->required(),
                                                    TextInput::make('nama_jabatan')
                                                        ->label('Nama Jabatan & Instansi')
                                                        ->placeholder('Contoh: Kepala Seksi Pelatihan di BPVP...')
                                                        ->required(),
                                                ])
                                                ->columns(2)
                                                ->createItemButtonLabel('Tambah Riwayat Jabatan')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(3)
                                        ->createItemButtonLabel('Tambah Pejabat'),
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

    public function save(): void
    {
        $validatedData = $this->form->getState();
        $record = $this->getRecord();

        if (! $record) {
            $record = new Profil();
            $record->id = 1;
        }

        $record->fill($validatedData);
        $record->save();

        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('Profil Balai berhasil diperbarui')
            ->send();
    }

    public function getRecord(): ?Profil
    {
        return Profil::query()->first();
    }
}
