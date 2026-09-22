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
        if (Auth::user()?->role === 'shortlink') {
            return redirect()->to(url('/admin/manage-shortlink'));
        }

        return redirect()->intended(Filament::getUrl());
    }
}
