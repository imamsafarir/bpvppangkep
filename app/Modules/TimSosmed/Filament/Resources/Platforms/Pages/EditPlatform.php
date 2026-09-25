<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Pages;

use App\Modules\TimSosmed\Filament\Resources\Platforms\PlatformResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditPlatform extends EditRecord
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
            DeleteAction::make(),
        ];
    }
}
