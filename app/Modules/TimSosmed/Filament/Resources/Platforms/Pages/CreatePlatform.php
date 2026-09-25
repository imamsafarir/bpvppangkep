<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Pages;

use App\Modules\TimSosmed\Filament\Resources\Platforms\PlatformResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreatePlatform extends CreateRecord
{
    protected static string $resource = PlatformResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
