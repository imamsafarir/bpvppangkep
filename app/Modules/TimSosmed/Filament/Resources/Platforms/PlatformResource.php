<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms;

use App\Modules\TimSosmed\Filament\Resources\Platforms\Pages\CreatePlatform;
use App\Modules\TimSosmed\Filament\Resources\Platforms\Pages\EditPlatform;
use App\Modules\TimSosmed\Filament\Resources\Platforms\Pages\ListPlatforms;
use App\Modules\TimSosmed\Filament\Resources\Platforms\Schemas\PlatformForm;
use App\Modules\TimSosmed\Filament\Resources\Platforms\Tables\PlatformsTable;
use App\Modules\TimSosmed\Models\Platform;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PlatformResource extends Resource
{
    protected static ?string $model = Platform::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';

    protected static ?string $navigationLabel = 'Platform Medsos';

    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return Auth::check() && (Auth::user()?->role === 'admin' || Auth::user()?->hasRole('super_admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return PlatformForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatformsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlatforms::route('/'),
            'create' => CreatePlatform::route('/create'),
            'edit' => EditPlatform::route('/{record}/edit'),
        ];
    }
}
