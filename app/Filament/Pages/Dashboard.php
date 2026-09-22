<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    /**
     * Dashboard utama hanya dapat diakses oleh admin/superadmin.
     * Role 'shortlink' disembunyikan total dari Dashboard dan navigasinya.
     */
    public static function canAccess(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function mountCanAuthorizeAccess(): void
    {
        if (Auth::user()?->role === 'shortlink') {
            redirect()->to(ManageShortlink::getUrl());
            return;
        }

        abort_unless(static::canAccess(), 403);
    }
}
