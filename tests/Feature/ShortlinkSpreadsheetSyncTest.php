<?php

namespace Tests\Feature;

use App\Modules\Shortlink\Models\Shortlink;
use App\Modules\Shortlink\Models\ShortlinkLead;
use App\Modules\Shortlink\Models\ShortlinkSetting;
use App\Modules\Shortlink\Services\ShortlinkSpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortlinkSpreadsheetSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_can_store_and_retrieve_values(): void
    {
        ShortlinkSetting::set('custom_test_key', 'custom_value');
        $this->assertEquals('custom_value', ShortlinkSetting::get('custom_test_key'));
        $this->assertEquals('fallback_default', ShortlinkSetting::get('non_existent', 'fallback_default'));
    }

    public function test_live_feed_csv_and_formula(): void
    {
        $formula = ShortlinkSpreadsheetService::getImportDataFormula();
        $this->assertStringStartsWith('=IMPORTDATA("', $formula);
        $this->assertStringContainsString('/shortlink/feed/leads.csv?token=', $formula);

        $token = ShortlinkSpreadsheetService::getOrCreateFeedToken();

        // Akses tanpa token atau token salah -> 403 Forbidden
        $this->get('/shortlink/feed/leads.csv')->assertStatus(403);
        $this->get('/shortlink/feed/leads.csv?token=wrong_token')->assertStatus(403);

        // Buat data shortlink dan data lead
        $shortlink = Shortlink::create([
            'pegawai_name'    => 'Staff Feed Test',
            'code'            => 'feed99',
            'destination_url' => 'https://example.com/target',
            'is_active'       => true,
        ]);

        ShortlinkLead::create([
            'shortlink_id' => $shortlink->id,
            'nama'         => 'Pengunjung CSV Feed',
            'whatsapp'     => '08123999888',
            'email'        => 'visitor@example.com',
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'PHPUnit Test',
        ]);

        // Akses dengan token valid -> 200 OK stream CSV
        $response = $this->get('/shortlink/feed/leads.csv?token=' . $token);
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Waktu Akses', $content);
        $this->assertStringContainsString('Staff Feed Test', $content);
        $this->assertStringContainsString('Pengunjung CSV Feed', $content);
        $this->assertStringContainsString('08123999888', $content);
    }
}
