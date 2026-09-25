<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Models\ContentRead;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class ViewContent extends ViewRecord
{
    protected static string $resource = ContentResource::class;

    protected string $view = 'timsosmed::filament.pages.view-content';

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (Auth::check() && $this->record) {
            ContentRead::updateOrCreate(
                [
                    'content_id' => $this->record->id,
                    'user_id' => Auth::id(),
                ],
                [
                    'last_read_at' => now(),
                ]
            );
        }
    }

    protected function getViewData(): array
    {
        $this->record->loadMissing([
            'instruktur',
            'pegawai',
            'planner',
            'editor',
            'admin',
            'platforms',
            'revisions.user',
            'comments.user',
            'media',
        ]);

        return array_merge(parent::getViewData(), [
            'record' => $this->record,
        ]);
    }

    public function downloadZip(string $collection)
    {
        $mediaItems = $this->record->getMedia($collection);

        if ($mediaItems->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('Tidak Ada Berkas')
                ->body('Tidak ditemukan berkas media fisik dalam kategori ini.')
                ->send();

            return null;
        }

        $zipFileName = 'konten-' . $this->record->id . '-' . $collection . '-' . date('YmdHis') . '.zip';
        $tempDir = storage_path('app/temp');

        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $filesAdded = 0;
            foreach ($mediaItems as $media) {
                $filePath = $media->getPath();
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $media->file_name);
                    $filesAdded++;
                }
            }
            $zip->close();

            if ($filesAdded > 0 && file_exists($zipPath)) {
                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            }
        }

        Notification::make()
            ->danger()
            ->title('Gagal Mengunduh Berkas')
            ->body('Berkas fisik media tidak ditemukan pada penyimpanan server.')
            ->send();

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn($record) => ContentResource::canEdit($record)),
        ];
    }
}
