<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,

    /*
    |--------------------------------------------------------------------------
    | Module Service Providers
    | Tambahkan service provider modul baru di sini
    |--------------------------------------------------------------------------
    */
    App\Modules\Website\WebsiteServiceProvider::class,
    App\Modules\Shortlink\ShortlinkServiceProvider::class,
];
