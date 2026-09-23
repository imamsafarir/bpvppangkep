<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth; // <-- Tambahkan ini

class ListContents extends ListRecords
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Konten Baru')
                // Tombol "New" HANYA tampil jika user adalah super_admin atau planner
                ->visible(fn() => Auth::user()->hasAnyRole(['super_admin', 'planner', 'instruktur', 'pegawai', 'editor', 'admin_platform'])),
        ];
    }
}
