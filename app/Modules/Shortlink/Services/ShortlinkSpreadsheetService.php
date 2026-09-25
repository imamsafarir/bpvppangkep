<?php

namespace App\Modules\Shortlink\Services;

use App\Modules\Shortlink\Models\ShortlinkSetting;

class ShortlinkSpreadsheetService
{
    /**
     * Dapatkan atau generate token akses feed rahasia
     */
    public static function getOrCreateFeedToken(): string
    {
        $token = ShortlinkSetting::get('spreadsheet_feed_token');

        if (empty($token)) {
            $token = bin2hex(random_bytes(16));
            ShortlinkSetting::set('spreadsheet_feed_token', $token);
        }

        return $token;
    }

    /**
     * Dapatkan URL lengkap endpoint live feed CSV
     */
    public static function getLiveFeedUrl(): string
    {
        $token = self::getOrCreateFeedToken();

        return route('shortlink.leads.feed', ['token' => $token]);
    }

    /**
     * Dapatkan rumus IMPORTDATA siap pakai untuk Google Spreadsheet
     */
    public static function getImportDataFormula(): string
    {
        $url = self::getLiveFeedUrl();

        return '=IMPORTDATA("' . $url . '")';
    }
}
