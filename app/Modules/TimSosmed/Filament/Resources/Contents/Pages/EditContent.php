<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Js;

class EditContent extends EditRecord
{
    protected static string $resource = ContentResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): ?string
    {
        return CalendarPage::getUrl();
    }

    protected function afterSave(): void
    {
        $this->js("
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ type: 'content-saved' }, '*');
            }
        ");
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn() => ! (Auth::user()?->isAdmin() ?? false))
                ->successRedirectUrl(CalendarPage::getUrl()),
        ];
    }

    protected function getFormActions(): array
    {
        if (! (Auth::user()?->isAdmin() ?? false)) {
            return [];
        }

        return parent::getFormActions();
    }

    protected function getCancelFormAction(): Action
    {
        $url = CalendarPage::getUrl();

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler("
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({ type: 'close-content-popup' }, '*');
                } else {
                    window.location.href = " . Js::from($url) . ";
                }
            ")
            ->color('gray');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();
        if ($user) {
            if ($user->isMedsosPlanner() && empty($data['planner_id'])) {
                $data['planner_id'] = $user->id;
            }
            if ($user->isMedsosEditor() && empty($data['editor_id'])) {
                $data['editor_id'] = $user->id;
            }
            if ($user->isMedsosAdminPlatform() && empty($data['admin_id'])) {
                $data['admin_id'] = $user->id;
            }
        }

        return $data;
    }
}
