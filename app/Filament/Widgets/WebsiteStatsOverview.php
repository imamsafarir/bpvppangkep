<?php

namespace App\Filament\Widgets;

use App\Models\Informasi;
use App\Models\InformasiPublik;
use App\Models\Jdih;
use App\Models\BeritaDanGaleri;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WebsiteStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '15s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $informasi = Informasi::query()->first();

        // CLOSURE HELPER: Untuk menghitung data berbasis JSON/Array
        $countJsonData = function ($value) {
            if (!$value) return 0;
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            return is_array($decoded) ? count($decoded) : 0;
        };

        // Kalkulasi data JSON dari model Informasi
        $totalKejuruan   = $countJsonData($informasi?->kejuruan);
        $totalFasilitas  = $countJsonData($informasi?->gedung_fasilitas);
        $totalWorkshop   = $countJsonData($informasi?->kelas_workshop);
        $totalAlumni     = $countJsonData($informasi?->alumni);
        $totalTestimoni  = $countJsonData($informasi?->testimoni);
        $totalKerjasama  = $countJsonData($informasi?->kerjasama);
        $totalFaq        = $countJsonData($informasi?->faq);

        // Kalkulasi jumlah baris data
        $totalInformasiPublik = InformasiPublik::query()->count('*');
        $totalJdih            = Jdih::query()->count('*');
        $totalBerita          = BeritaDanGaleri::query()->where('jenis', 'berita')->count('*');
        $totalGaleri          = BeritaDanGaleri::query()->where('jenis', 'galeri')->count('*');

        // Menggunakan field 'jumlah_diunduh' untuk akumulasi total download berkas
        $unduhanPPID = InformasiPublik::query()->sum('jumlah_diunduh') ?? 0;
        $unduhanJDIH = Jdih::query()->sum('jumlah_diunduh') ?? 0;
        $grandTotalUnduhan = $unduhanPPID + $unduhanJDIH;

        return [
            /* ================= BLOCK 1: ANALITIK UTAMA ================= */
            Stat::make('Total Unduhan Berkas', number_format($grandTotalUnduhan) . ' Kali')
                ->description('Total download dokumen PPID & JDIH')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->chart([3, 8, 5, 12, 9, 17, 24])
                ->color('success')
                ->url(url('/admin/informasi-publiks')), // 💡 Diarahkan ke daftar berkas PPID

            /* ================= BLOCK 2: AKADEMIK & SARANA ================= */
            Stat::make('Total Kejuruan', $totalKejuruan . ' Kelas')
                ->description('Kejuruan resmi aktif di balai')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary')
                ->url(url('/admin/manage-informasi')), // 💡 Sesuaikan dengan slug halaman edit Informasi kamu

            Stat::make('Gedung & Fasilitas', $totalFasilitas . ' Unit')
                ->description('Sarana penunjang operasional')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success')
                ->url(url('/admin/manage-informasi')),

            Stat::make('Kelas Workshop', $totalWorkshop . ' Ruangan')
                ->description('Tempat pelatihan/praktik kerja')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info')
                ->url(url('/admin/manage-informasi')),

            /* ================= BLOCK 3: KEMITRAAN & EKSTERNAL ================= */
            Stat::make('Tracer Alumni', $totalAlumni . ' Orang')
                ->description('Alumni terdata di sistem')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->url(url('/admin/manage-informasi')),

            Stat::make('Testimoni Publik', $totalTestimoni . ' Ulasan')
                ->description('Ulasan alumni & mitra kerja')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->url(url('/admin/manage-informasi')),

            Stat::make('Mitra Kerjasama', $totalKerjasama . ' Instansi')
                ->description('Hubungan industri aktif')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning')
                ->url(url('/admin/manage-informasi')),

            /* ================= BLOCK 4: KONTEN & PUBLIKASI ================= */
            Stat::make('Artikel Berita', $totalBerita . ' Post')
                ->description('Kabar berita balai yang terbit')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('info')
                ->url(url('/admin/manage-berita-dan-galeri')), // 💡 Ke halaman Custom Page Berita & Galeri yang kita buat kemarin

            Stat::make('Galeri Kegiatan', $totalGaleri . ' Album')
                ->description('Dokumentasi visual balai')
                ->descriptionIcon('heroicon-m-photo')
                ->color('success')
                ->url(url('/admin/manage-berita-dan-galeri')), // 💡 Sama-sama ke halaman managemen kustomnya

            Stat::make('Dokumen PPID', $totalInformasiPublik . ' Berkas')
                ->description('Total informasi publik berkala')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning')
                ->url(url('/admin/informasi-publiks')), // 💡 Ke Resource Informasi Publik

            /* ================= BLOCK 5: REGULASI & BANTUAN ================= */
            Stat::make('Produk Hukum JDIH', $totalJdih . ' Regulasi')
                ->description('Peraturan resmi terunggah')
                ->descriptionIcon('heroicon-m-scale')
                ->color('danger')
                ->url(url('/admin/jdihs')), // 💡 Diarahkan ke Resource CRUD JDIH

            Stat::make('Pertanyaan FAQ', $totalFaq . ' Butir')
                ->description('Pusat bantuan & tanya jawab')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('gray')
                ->url(url('/admin/manage-informasi')),
        ];
    }
}
