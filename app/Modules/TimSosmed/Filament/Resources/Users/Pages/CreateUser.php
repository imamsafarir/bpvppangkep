<?php

namespace App\Modules\TimSosmed\Filament\Resources\Users\Pages;

use App\Modules\TimSosmed\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
