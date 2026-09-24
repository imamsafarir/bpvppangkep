<?php

namespace App\Filament\Pages;

use App\Models\AuthenticationLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * =========================================================
 * PAGE: MANAGE USER (PREMIUM EDITION - FULL WIDTH & SPATIE ROLE)
 * =========================================================
 */
class ManageUser extends Page
{
    protected string $view = 'filament.pages.manage-user';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?string $title = 'Kelola Pengguna Sistem';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected string | Width | null $maxContentWidth = 'full';

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan Sistem';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * 🟢 FORM UTAMA: Pendaftaran User Baru
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Pendaftaran Pengguna Baru')
                        ->description('Isi data di bawah ini untuk membuat akun pengguna baru beserta peran/hak aksesnya.')
                        ->icon('heroicon-o-user-plus')
                        ->schema([
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
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

                                    Select::make('roles')
                                        ->label('Peran / Hak Akses Pengguna (Bisa Lebih Dari 1)')
                                        ->options([
                                            'admin'                  => '👑 Administrator System',
                                            'staff'                  => '💼 Staf Balai',
                                            'shortlink'              => '🔗 Admin Shortlink',
                                            'website'                => '🌐 Pengelola Website',
                                            'medsos_instruktur'      => '👨‍🏫 Medsos Instruktur',
                                            'medsos_planner'         => '📋 Medsos Planner',
                                            'medsos_editor'          => '🎨 Medsos Editor',
                                            'medsos_admin_platform'  => '🚀 Medsos Admin Platform',
                                            'user'                   => '👥 Pengguna Biasa',
                                        ])
                                        ->multiple()
                                        ->required()
                                        ->native(false)
                                        ->default(['user'])
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Daftarkan User Baru')
                                ->icon('heroicon-m-user-plus')
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
        $roles = $validatedData['roles'] ?? ['user'];
        $validatedData['role'] = is_array($roles) ? implode(',', $roles) : (string) $roles;
        unset($validatedData['roles']);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        $this->form->fill();
        $this->dispatch('refreshTables');

        Notification::make()
            ->title('User Berhasil Didaftarkan')
            ->success()
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            DaftarUserTable::class,
            DaftarLogAktivitasTable::class,
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
            ->query(User::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->description(fn($record) => '@' . $record->username)
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('gray'),

                TextColumn::make('roles_list')
                    ->label('Peran / Hak Akses')
                    ->badge()
                    ->separator(',')
                    ->color(fn(string $state): string => match ($state) {
                        'admin', 'super_admin'                     => 'danger',
                        'staff', 'pegawai'                         => 'warning',
                        'shortlink'                                => 'info',
                        'website'                                  => 'primary',
                        'medsos_instruktur', 'instruktur'          => 'secondary',
                        'medsos_planner', 'planner'                 => 'warning',
                        'medsos_editor', 'editor'                  => 'success',
                        'medsos_admin_platform', 'admin_platform'  => 'info',
                        'user'                                     => 'gray',
                        default                                    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'admin', 'super_admin'                     => '👑 Administrator',
                        'staff', 'pegawai'                         => '💼 Staf Balai',
                        'shortlink'                                => '🔗 Admin Shortlink',
                        'website'                                  => '🌐 Pengelola Web',
                        'medsos_instruktur', 'instruktur'          => '👨‍🏫 Medsos Instruktur',
                        'medsos_planner', 'planner'                 => '📋 Medsos Planner',
                        'medsos_editor', 'editor'                  => '🎨 Medsos Editor',
                        'medsos_admin_platform', 'admin_platform'  => '🚀 Medsos Admin Platform',
                        'user'                                     => '👥 Pengguna Biasa',
                        default                                    => ucfirst($state),
                    })
                    ->searchable(query: fn($query, string $search) => $query->where('role', 'like', "%{$search}%")),

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
                                ->unique(User::class, 'username', ignoreRecord: true),

                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->unique(User::class, 'email', ignoreRecord: true),

                            TextInput::make('password')
                                ->label('Ganti Password Baru')
                                ->placeholder('Kosongkan jika tidak diubah')
                                ->password()
                                ->revealable()
                                ->nullable(),

                            Select::make('roles')
                                ->label('Peran / Hak Akses Pengguna (Bisa Lebih Dari 1)')
                                ->options([
                                    'admin'                  => '👑 Administrator System',
                                    'staff'                  => '💼 Staf Balai',
                                    'shortlink'              => '🔗 Admin Shortlink',
                                    'website'                => '🌐 Pengelola Website',
                                    'medsos_instruktur'      => '👨‍🏫 Medsos Instruktur',
                                    'medsos_planner'         => '📋 Medsos Planner',
                                    'medsos_editor'          => '🎨 Medsos Editor',
                                    'medsos_admin_platform'  => '🚀 Medsos Admin Platform',
                                    'user'                   => '👥 Pengguna Biasa',
                                ])
                                ->multiple()
                                ->required()
                                ->native(false)
                                ->formatStateUsing(fn(User $record) => $record->roles_list)
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->using(function (User $record, array $data): User {
                        if (filled($data['password'] ?? null)) {
                            $data['password'] = Hash::make($data['password']);
                        } else {
                            unset($data['password']);
                        }

                        if (isset($data['roles'])) {
                            $roles = is_array($data['roles']) ? $data['roles'] : [$data['roles']];
                            $data['role'] = implode(',', $roles);
                            unset($data['roles']);
                        }

                        $record->update($data);

                        return $record;
                    })
                    ->successNotificationTitle('Data pengguna dan hak akses berhasil diperbarui'),

                DeleteAction::make(),
            ]);
    }
}

/**
 * =========================================================
 * WIDGET: LOG AKTIVITAS LOGIN & LOGOUT USER
 * =========================================================
 */
class DaftarLogAktivitasTable extends TableWidget
{
    protected static ?string $heading = '🕒 Riwayat Aktivitas Login & Logout Pengguna';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshTables' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(AuthenticationLog::query()->with('user')->latest('id'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->icon('heroicon-m-clock'),

                TextColumn::make('event_type')
                    ->label('Aktivitas')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'login'  => 'success',
                        'logout' => 'warning',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'login'  => '🟢 Masuk (Login)',
                        'logout' => '🟡 Keluar (Logout)',
                        default  => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->default(fn(AuthenticationLog $record) => $record->username ?? '-')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-user'),

                TextColumn::make('user.roles_list')
                    ->label('Peran Sistem')
                    ->badge()
                    ->separator(',')
                    ->color(fn(?string $state): string => match ($state) {
                        'admin', 'super_admin'                     => 'danger',
                        'staff', 'pegawai'                         => 'warning',
                        'shortlink'                                => 'info',
                        'website'                                  => 'primary',
                        'medsos_instruktur', 'instruktur'          => 'secondary',
                        'medsos_planner', 'planner'                 => 'warning',
                        'medsos_editor', 'editor'                  => 'success',
                        'medsos_admin_platform', 'admin_platform'  => 'info',
                        'user'                                     => 'gray',
                        default                                    => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'admin', 'super_admin'                     => '👑 Administrator',
                        'staff', 'pegawai'                         => '💼 Staf Balai',
                        'shortlink'                                => '🔗 Admin Shortlink',
                        'website'                                  => '🌐 Pengelola Web',
                        'medsos_instruktur', 'instruktur'          => '👨‍🏫 Medsos Instruktur',
                        'medsos_planner', 'planner'                 => '📋 Medsos Planner',
                        'medsos_editor', 'editor'                  => '🎨 Medsos Editor',
                        'medsos_admin_platform', 'admin_platform'  => '🚀 Medsos Admin Platform',
                        'user'                                     => '👥 Pengguna Biasa',
                        default                                    => ucfirst((string) $state),
                    })
                    ->default('-'),

                TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->icon('heroicon-m-globe-alt')
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('user_agent')
                    ->label('Browser / Perangkat')
                    ->limit(50)
                    ->tooltip(fn(AuthenticationLog $record): ?string => $record->user_agent)
                    ->color('gray')
                    ->size('xs'),
            ])
            ->paginated([10, 25, 50]);
    }
}
