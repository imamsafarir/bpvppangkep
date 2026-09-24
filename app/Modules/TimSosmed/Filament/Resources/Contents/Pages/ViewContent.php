<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class ViewContent extends ViewRecord
{
    protected static string $resource = ContentResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali_kalender')
                ->label('📅 Kembali ke Kalender')
                ->color('gray')
                ->url(fn() => CalendarPage::getUrl()),

            EditAction::make()
                ->visible(function ($record) {
                    $user = Auth::user();

                    if (! $user) {
                        return false;
                    }

                    if ($record->status !== 'selesai') {
                        return true;
                    }

                    return $user->isAdmin();
                }),
        ];
    }
}
