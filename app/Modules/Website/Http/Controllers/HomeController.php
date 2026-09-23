<?php

namespace App\Modules\Website\Http\Controllers;

use App\Modules\Website\Models\Jdih;
use App\Modules\Website\Models\WebsiteSetting;
use App\Modules\Website\Models\BeritaDanGaleri;
use App\Modules\Website\Models\InformasiPublik;
use App\Modules\Website\Models\Kunjungan;
use Illuminate\Http\Request;

class HomeController
{
    public function index(Request $request)
    {
        Kunjungan::firstOrCreate([
            'ip_address' => $request->ip(),
            'tanggal'    => now()->toDateString(),
        ]);

        $settings        = WebsiteSetting::query()->first();
        $total_informasi = InformasiPublik::query()->count('*');
        $total_jdih      = Jdih::query()->where('status_peraturan', 'berlaku')->count('*');

        $berita_query   = BeritaDanGaleri::query()->where('jenis', 'berita');
        $berita_terbaru = $berita_query->latest()->limit(4)->get();
        $total_berita   = $berita_query->count('*');

        $total_unduhan   = InformasiPublik::sum('jumlah_diunduh') + Jdih::sum('jumlah_diunduh');
        $total_kunjungan = Kunjungan::count('*');

        $informasi = InformasiPublik::query()->latest()->get();
        $jdih      = Jdih::query()->where('status_peraturan', 'berlaku')->latest()->limit(5)->get();

        return view('website::home', [
            'settings'        => $settings,
            'berita_terbaru'  => $berita_terbaru,
            'informasi'       => $informasi,
            'jdih'            => $jdih,
            'total_informasi' => $total_informasi,
            'total_jdih'      => $total_jdih,
            'total_berita'    => $total_berita,
            'total_unduhan'   => $total_unduhan,
            'total_kunjungan' => $total_kunjungan,
        ]);
    }
}
