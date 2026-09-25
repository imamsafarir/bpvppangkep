<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Widget anggota tim medsos aktif — menampilkan data real anggota
 * yang memiliki role tim sosmed beserta statistik konten.
 */
class ActiveTeamWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = '👥 Anggota Tim Medsos';

    protected static ?int $maxHeight = 400;

    /**
     * Mapping label role manusiawi.
     */
    protected array $roleLabels = [
        'super_admin'            => 'Super Admin',
        'admin'                  => 'Administrator',
        'medsos_planner'         => 'Medsos Planner',
        'planner'                => 'Medsos Planner',
        'medsos_editor'          => 'Medsos Editor',
        'editor'                 => 'Medsos Editor',
        'medsos_admin_platform'  => 'Medsos Admin Platform',
        'admin_platform'         => 'Medsos Admin Platform',
        'medsos_instruktur'      => 'Medsos Instruktur',
        'instruktur'             => 'Medsos Instruktur',
    ];

    /**
     * Mapping warna badge role.
     */
    protected array $roleColors = [
        'admin'                  => 'danger',
        'super_admin'            => 'danger',
        'medsos_planner'         => 'warning',
        'planner'                => 'warning',
        'medsos_editor'          => 'success',
        'editor'                 => 'success',
        'medsos_admin_platform'  => 'info',
        'admin_platform'         => 'info',
        'medsos_instruktur'      => 'primary',
        'instruktur'             => 'primary',
    ];

    public static function canView(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isMedsosTeam() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::medsosTeam()->orderBy('name')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->description(fn($record) => '@' . $record->username)
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => $this->roleLabels[$state] ?? ucfirst($state))
                    ->color(fn(string $state): string => $this->roleColors[$state] ?? 'gray'),

                Tables\Columns\TextColumn::make('active_tasks')
                    ->label('Tugas Aktif')
                    ->getStateUsing(fn($record) => $record->activeTasks()->count())
                    ->badge()
                    ->color(fn($state): string => $state > 0 ? 'danger' : 'gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->color('gray')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('Belum ada anggota tim')
            ->emptyStateDescription('Tambahkan atau atur peran tim medsos melalui menu Kelola Pengguna.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
