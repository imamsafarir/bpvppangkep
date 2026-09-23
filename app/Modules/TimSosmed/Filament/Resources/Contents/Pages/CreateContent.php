<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CreateContent extends CreateRecord
{
    protected static string $resource = ContentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // RESET SEMUA ID DULU
        $data['planner_id'] = null;
        $data['instruktur_id'] = null;
        $data['editor_id'] = null;
        $data['admin_id'] = null;

        // ISI SESUAI ROLE
        if (Auth::user()->hasRole('instruktur')) {
            $data['instruktur_id'] = Auth::id();
        } else {
            // Jika Planner/Admin yang buat
            $data['planner_id'] = Auth::id();
        }

        return $data;
    }
}
