<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents;

use App\Modules\TimSosmed\Filament\Resources\Contents\Infolists\ContentInfolist;
use App\Modules\TimSosmed\Filament\Resources\Contents\Pages\CreateContent;
use App\Modules\TimSosmed\Filament\Resources\Contents\Pages\EditContent;
use App\Modules\TimSosmed\Filament\Resources\Contents\Pages\ListContents;
use App\Modules\TimSosmed\Filament\Resources\Contents\Pages\ViewContent;
use App\Modules\TimSosmed\Filament\Resources\Contents\Schemas\ContentForm;
use App\Modules\TimSosmed\Filament\Resources\Contents\Tables\ContentsTable;
use App\Modules\TimSosmed\Models\Content;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Manajemen Konten';

    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';

    protected static ?int $navigationSort = 1;

    protected static function internalRoles(): array
    {
        return [
            'super_admin',
            'admin_platform',
            'planner',
            'editor',
            'instruktur',
            'pegawai',
        ];
    }

    protected static function canAccessInternal(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        if ($user->role === 'admin' || $user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasAnyRole(static::internalRoles());
    }

    public static function canAccess(): bool
    {
        return static::canAccessInternal();
    }

    public static function canViewAny(): bool
    {
        return static::canAccessInternal();
    }

    public static function canCreate(): bool
    {
        return static::canAccessInternal();
    }

    public static function canView(Model $record): bool
    {
        return static::canAccessInternal();
    }

    public static function canEdit(Model $record): bool
    {
        if (! static::canAccessInternal()) {
            return false;
        }

        $user = Auth::user();
        if ($user?->role === 'admin' || $user?->hasRole('super_admin')) {
            return true;
        }

        if ($record->status === 'selesai') {
            return false;
        }

        return true;
    }

    public static function canDelete(Model $record): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();
        if ($user?->role === 'admin' || $user?->hasRole('super_admin')) {
            return true;
        }

        return $user?->hasRole('planner') && $record->status !== 'selesai';
    }

    public static function canDeleteAny(): bool
    {
        $user = Auth::user();
        return Auth::check() && ($user?->role === 'admin' || $user?->hasRole('super_admin'));
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentInfolist::configure($schema);
    }

    public static function form(Schema $schema): Schema
    {
        return ContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (! Auth::check()) {
            return $query->whereRaw('1 = 0');
        }

        $user = Auth::user();
        if ($user->role === 'admin' || $user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('pegawai')) {
            return $query->where('pegawai_id', Auth::id());
        }

        if ($user->hasRole('instruktur')) {
            return $query->where('instruktur_id', Auth::id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContents::route('/'),
            'create' => CreateContent::route('/create'),
            'view' => ViewContent::route('/{record}'),
            'edit' => EditContent::route('/{record}/edit'),
        ];
    }
}
