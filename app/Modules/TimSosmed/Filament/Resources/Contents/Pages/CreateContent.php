<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Js;
use App\Models\User;

class CreateContent extends CreateRecord
{
    protected static string $resource = ContentResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return CalendarPage::getUrl();
    }

    protected function afterCreate(): void
    {
        $this->js("
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ type: 'content-saved' }, '*');
            }
        ");
    }

    protected function getCancelFormAction(): Action
    {
        $url = CalendarPage::getUrl();

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler("
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({ type: 'close-content-popup' }, '*');
                } else {
                    window.location.href = " . Js::from($url) . ";
                }
            ")
            ->color('gray');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // RESET SEMUA ID DULU
        $data['planner_id'] = null;
        $data['instruktur_id'] = null;
        $data['editor_id'] = null;
        $data['admin_id'] = null;

        $user = Auth::user();
        if ($user) {
            if ($user->isInstruktur()) {
                $data['instruktur_id'] = $user->id;
            }
            if ($user->isMedsosPlanner() || $user->isAdmin()) {
                $data['planner_id'] = $user->id;
            }
        }

        return $data;
    }
}
