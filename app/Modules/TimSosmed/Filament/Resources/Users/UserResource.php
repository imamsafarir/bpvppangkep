<?php

namespace App\Modules\TimSosmed\Filament\Resources\Users;

use App\Modules\TimSosmed\Filament\Resources\Users\Pages\CreateUser;
use App\Modules\TimSosmed\Filament\Resources\Users\Pages\EditUser;
use App\Modules\TimSosmed\Filament\Resources\Users\Pages\ListUsers;
use App\Modules\TimSosmed\Filament\Resources\Users\Schemas\UserForm;
use App\Modules\TimSosmed\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';

    protected static ?string $navigationLabel = 'Anggota Tim Medsos';

    protected static ?int $navigationSort = 6;

    // FUNGSI PENGUNCI: Hanya Super Admin / Admin yang bisa melihat menu ini
    public static function canViewAny(): bool
    {
        return Auth::check() && (Auth::user()?->role === 'admin' || Auth::user()?->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
