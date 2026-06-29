<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

/* ===========================
| FORM COMPONENTS
=========================== */
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select; // 💡 FIX IMPORT: Menambahkan class Select untuk dropdown role
use Filament\Schemas\Components\Grid;

/* ===========================
| TABLE & WIDGET
=========================== */
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

/**
 * =========================================================
 * PAGE: MANAGE USER (PREMIUM EDITION - GRID & ROLE INTEGRATED)
 * =========================================================
 */
class ManageUser extends Page
{
    protected string $view = 'filament.pages.manage-user';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?string $title = 'Kelola Pengguna Sistem';
    protected static ?string $navigationLabel = 'Manajemen User';

    /**
     * Proteksi halaman agar hanya level 'admin' yang bisa masuk rute ini
     */
    public static function canAccess(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * 🟢 FORM UTAMA: Pendaftaran User Baru (Bagian Atas)
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->placeholder('Masukkan nama lengkap pengguna...')
                        ->prefixIcon('heroicon-m-user')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('username')
                        ->label('Username Login')
                        ->placeholder('Contoh: ppid.pangkep')
                        ->prefixIcon('heroicon-m-identification')
                        ->required()
                        ->unique(User::class, ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->placeholder('Contoh: admin@bpvppangkep.go.id')
                        ->prefixIcon('heroicon-m-envelope')
                        ->email()
                        ->required()
                        ->unique(User::class, ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label('Password Akses')
                        ->placeholder('••••••••')
                        ->prefixIcon('heroicon-m-lock-closed')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255),

                    // 🟢 TAMBAHAN: Pilihan Role saat mendaftarkan user baru
                    Select::make('role')
                        ->label('Hak Akses / Role')
                        ->options([
                            'admin' => '👑 Administrator',
                            'staff' => '💼 Staf Balai',
                            'user'  => '👥 Pengguna Biasa',
                        ])
                        ->required()
                        ->default('user')
                        ->columnSpanFull(),
                ])
                    ->columns(2)
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Daftarkan User Baru')
                                ->submit('save')
                                ->color('primary'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $validatedData = $this->form->getState();
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        $this->form->fill();
        $this->dispatch('refreshTables');

        Notification::make()
            ->success()
            ->title('Pengguna baru berhasil didaftarkan!')
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            DaftarUserTable::class,
        ];
    }
}

/**
 * =========================================================
 * WIDGET: DAFTAR DATABASE USER DENGAN BADGE ROLE KONTRAS
 * =========================================================
 */
class DaftarUserTable extends TableWidget
{
    protected static ?string $heading = '👥 Database Pengguna Terdaftar';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshTables' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->icon('heroicon-m-user')
                    ->iconColor('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('username')
                    ->label('Username')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('gray'),

                // 🟢 TAMBAHAN: Kolom Role berwujud badge adaptif warna
                TextColumn::make('role')
                    ->label('Hak Akses')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'danger',  // Merah kontras untuk Admin
                        'staff' => 'warning', // Kuning/Amber untuk Staff
                        'user'  => 'success', // Hijau untuk User biasa
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Registrasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->color('warning')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required(),

                            TextInput::make('username')
                                ->label('Username')
                                ->required()
                                ->unique(User::class, ignoreRecord: true),

                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->unique(User::class, ignoreRecord: true),

                            TextInput::make('password')
                                ->label('Ganti Password Baru')
                                ->placeholder('Kosongkan jika tidak diubah')
                                ->password()
                                ->revealable()

                                // 🟢 KUNCI UTAMA: Paksa inputan jadi kosong saat form edit dimuat
                                ->afterStateHydrated(fn(\Filament\Forms\Components\TextInput $component) => $component->state(''))

                                // Hanya lakukan hash jika user benar-benar mengetik sesuatu (tidak kosong)
                                ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                ->dehydrated(fn($state) => filled($state)),

                            Select::make('role')
                                ->label('Hak Akses / Role')
                                ->options([
                                    'admin' => '👑 Administrator',
                                    'staff' => '💼 Staf Balai',
                                    'user'  => '👥 Pengguna Biasa',
                                ])
                                ->required()
                                ->default('user'),
                        ]),
                    ]),
                DeleteAction::make(),
            ]);
    }
}
