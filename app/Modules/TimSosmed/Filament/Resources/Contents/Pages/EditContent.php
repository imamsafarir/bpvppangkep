<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
// PERBAIKAN: Gunakan Action universal dari Filament\Actions
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditContent extends EditRecord
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn() => ! auth()->user()->hasRole('super_admin')),
        ];
    }

    /**
     * Logika otomatis setelah tombol 'Save Changes' diklik
     */
    protected function afterSave(): void
    {
        $record = $this->record;
        $targetUrl = ContentResource::getUrl('edit', ['record' => $record]);

        // 1. REVISI UNTUK EDITOR (Hanya kirim ke Editor)
        if ($record->status === 'revisi_editor') {
            $editors = User::role('editor')->get();
            Notification::make()
                ->title('🛠️ Ada Revisi Editor!')
                ->body("Admin meminta perbaikan aset untuk '{$record->nama_kegiatan}'.")
                ->danger()
                ->actions([
                    Action::make('view')
                        ->label('Buka Konten')
                        ->url($targetUrl)
                        ->button()
                        ->markAsRead(),
                ])
                ->sendToDatabase($editors);
        }

        // 2. REVISI UNTUK PLANNER (Hanya kirim ke Planner)
        if ($record->status === 'revisi_planner') {
            if ($record->planner) {
                Notification::make()
                    ->title('📝 Ada Revisi Planner!')
                    ->body("Konten '{$record->nama_kegiatan}' perlu perbaikan brief/caption.")
                    ->danger()
                    ->actions([
                        Action::make('view')
                            ->label('Buka Konten')
                            ->url($targetUrl)
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($record->planner);
            }
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->hasRole(['planner', 'super_admin'])) {
            $data['planner_id'] = auth()->id();
        }

        return $data;
    }
}
