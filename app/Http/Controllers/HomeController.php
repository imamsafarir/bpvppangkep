<?php

namespace App\Http\Controllers;

use App\Models\Jdih;
use App\Models\WebsiteSetting;
use App\Models\BeritaDanGaleri;
use App\Models\InformasiPublik;
use App\Models\Kunjungan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. LOGIKA TRACKING KUNJUNGAN (Unik per IP per Hari)
        Kunjungan::firstOrCreate([
            'ip_address' => $request->ip(),
            'tanggal'    => now()->toDateString(),
        ]);

        // 2. AMBIL DATA SETTING WEBSITE
        $settings = WebsiteSetting::query()->first();

        // 3. AMBIL DATA STATISTIK
        $total_informasi = InformasiPublik::query()->count('*');
        $total_jdih      = Jdih::query()->where('status_peraturan', 'berlaku')->count('*');

        // Berita & Galeri
        $berita_query    = BeritaDanGaleri::query()->where('jenis', 'berita');
        $berita_terbaru  = $berita_query->latest()->limit(4)->get();
        $total_berita    = $berita_query->count('*');

        // Statistik Real-Time: Total Unduhan dari PPID dan JDIH
        $total_unduhan   = InformasiPublik::sum('jumlah_diunduh') + Jdih::sum('jumlah_diunduh');
        // dd($total_unduhan);

        // Statistik Real-Time: Total Pengunjung
        $total_kunjungan = Kunjungan::count('*');

        // 4. DATA LAINNYA
        $informasi = InformasiPublik::query()->latest()->get();
        $jdih      = Jdih::query()->where('status_peraturan', 'berlaku')->latest()->limit(5)->get();

        // 5. KIRIM KE VIEW
        return view('home', [
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
