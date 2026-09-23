<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Pages;

use App\Modules\TimSosmed\Filament\Resources\Platforms\PlatformResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlatform extends EditRecord
{
    protected static string $resource = PlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
