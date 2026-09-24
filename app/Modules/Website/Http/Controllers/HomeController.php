<?php

namespace App\Modules\Website\Http\Controllers;

use App\Modules\Website\Models\Jdih;
use App\Modules\Website\Models\WebsiteSetting;
use App\Modules\Website\Models\BeritaDanGaleri;
use App\Modules\Website\Models\InformasiPublik;
use App\Modules\Website\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController
{
    public function index(Request $request)
    {
        try {
            Kunjungan::firstOrCreate([
                'ip_address' => $request->ip(),
                'tanggal'    => now()->toDateString(),
            ]);
        } catch (\Throwable $e) {
            // Abaikan jika terjadi concurrency race condition
        }

        $fetchHomeData = function () {
            $settings        = WebsiteSetting::query()->first();
            $total_informasi = InformasiPublik::query()->count('*');
            $total_jdih      = Jdih::query()->where('status_peraturan', 'berlaku')->count('*');

            $berita_query   = BeritaDanGaleri::query()->where('jenis', 'berita');
            $berita_terbaru = $berita_query->latest()->limit(4)->get();
            $total_berita   = $berita_query->count('*');

            $total_unduhan   = (int) (InformasiPublik::sum('jumlah_diunduh') + Jdih::sum('jumlah_diunduh'));
            $total_kunjungan = (int) Kunjungan::count('*');

            $informasi = InformasiPublik::query()->latest()->limit(15)->get();
            $jdih      = Jdih::query()->where('status_peraturan', 'berlaku')->latest()->limit(5)->get();

            return [
                'settings'        => $settings,
                'berita_terbaru'  => $berita_terbaru,
                'informasi'       => $informasi,
                'jdih'            => $jdih,
                'total_informasi' => $total_informasi,
                'total_jdih'      => $total_jdih,
                'total_berita'    => $total_berita,
                'total_unduhan'   => $total_unduhan,
                'total_kunjungan' => $total_kunjungan,
            ];
        };

        try {
            $cachedData = Cache::remember('website_home_data', 300, $fetchHomeData);

            // Self-healing: jika cache berisi incomplete class dari cache driver/versi lama, hapus dan ambil data segar
            if (
                ! is_array($cachedData) ||
                ($cachedData['berita_terbaru'] ?? null) instanceof \__PHP_Incomplete_Class ||
                ($cachedData['informasi'] ?? null) instanceof \__PHP_Incomplete_Class ||
                ($cachedData['jdih'] ?? null) instanceof \__PHP_Incomplete_Class ||
                ($cachedData['settings'] ?? null) instanceof \__PHP_Incomplete_Class
            ) {
                Cache::forget('website_home_data');
                $cachedData = $fetchHomeData();
            }
        } catch (\Throwable $e) {
            $cachedData = $fetchHomeData();
        }

        return view('website::home', $cachedData);
    }
}
