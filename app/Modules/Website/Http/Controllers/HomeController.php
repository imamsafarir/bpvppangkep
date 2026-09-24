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

        // Simpan hasil query data beranda dalam cache selama 5 menit
        $cachedData = Cache::remember('website_home_data', 300, function () {
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
        });

        return view('website::home', $cachedData);
    }
}
