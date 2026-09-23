<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Pages;

use App\Modules\TimSosmed\Filament\Resources\Platforms\PlatformResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlatforms extends ListRecords
{
    protected static string $resource = PlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
