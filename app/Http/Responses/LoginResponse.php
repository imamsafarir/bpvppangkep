<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = Auth::user();

        if ($user) {
            if ($user->isAdmin()) {
                return redirect()->intended(Filament::getUrl());
            }

            return redirect()->to($user->getDefaultDashboardUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}
