<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Pages;

use App\Modules\TimSosmed\Filament\Resources\Platforms\PlatformResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListPlatforms extends ListRecords
{
    protected static string $resource = PlatformResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
