<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WebsitePerformanceAndCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_home_page_returns_cache_control_and_etag()
    {
        Cache::flush();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('ETag');
        $this->assertStringContainsString('public', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=180', $response->headers->get('Cache-Control'));

        // Pastikan query cache database tersimpan
        $this->assertTrue(Cache::has('website_home_data'));
    }

    public function test_home_page_returns_304_when_etag_matches()
    {
        // Request pertama untuk mendapatkan ETag
        $firstResponse = $this->get('/');
        $firstResponse->assertStatus(200);
        $etag = $firstResponse->headers->get('ETag');

        $this->assertNotEmpty($etag);

        // Request kedua dengan header If-None-Match yang sama
        $secondResponse = $this->withHeaders([
            'If-None-Match' => $etag,
        ])->get('/');

        $secondResponse->assertStatus(304);
    }

    public function test_profil_page_caches_common_data()
    {
        Cache::flush();

        $response = $this->get(route('profil.visi-misi'));

        $response->assertStatus(200);
        $response->assertHeader('ETag');

        // Pastikan common data profil tersimpan dalam cache
        $this->assertTrue(Cache::has('website_common_data'));
    }

    public function test_home_page_does_not_contain_heavy_tailwind_browser_cdn()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Pastikan @tailwindcss/browser yang memberatkan CPU browser sudah dihapus
        $response->assertDontSee('@tailwindcss/browser@4');
        // Pastikan registrasi Service Worker aktif
        $response->assertSee('navigator.serviceWorker.register');
    }

    public function test_service_worker_file_exists()
    {
        $this->assertFileExists(public_path('sw.js'));
        $content = file_get_contents(public_path('sw.js'));
        $this->assertStringContainsString('bpvp-client-cache-v2', $content);
        $this->assertStringContainsString('cache.match(request)', $content);
    }
}
