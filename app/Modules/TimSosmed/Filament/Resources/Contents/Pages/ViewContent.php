<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth; // Tambahkan import ini

class ViewContent extends ViewRecord
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kita gunakan Auth facade supaya Intelephense tahu ini adalah user
            Actions\EditAction::make()
                ->visible(function ($record) {
                    $user = Auth::user();

                    // Jika status belum selesai, semua yang punya akses boleh edit
                    if ($record->status !== 'selesai') {
                        return true;
                    }

                    // Jika sudah selesai, hanya super_admin yang boleh lihat tombol edit
                    return $user && $user->hasRole('super_admin');
                }),
        ];
    }
}
