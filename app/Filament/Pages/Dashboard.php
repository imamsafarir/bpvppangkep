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
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->isAdmin();
    }

    public function mountCanAuthorizeAccess(): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if (! static::canAccess()) {
            $this->redirect($user->getDefaultDashboardUrl());
            return;
        }
    }
}
