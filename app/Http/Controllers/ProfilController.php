<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Informasi;
use App\Models\InformasiPublik; // Ambil model Informasi

class ProfilController extends Controller
{
    // Method Helper Privat agar tidak menulis kode query berulang-ulang
    private function getCommonData()
    {
        // Ambil data baris pertama dari tabel informasis
        $informasiRow = \App\Models\Informasi::query()->first();

        return [
            'profil' => DB::table('profils')->first(),
            'settings' => DB::table('website_settings')->first(),
            'pelayanan' => \App\Models\PelayananPublik::query()->first(),

            // Tambahkan data extract di bawah ini agar terbaca di masing-masing view informasi
            'kejuruan' => $informasiRow?->kejuruan ?? [],
            'fasilitas' => $informasiRow?->gedung_fasilitas ?? [],
            'workshop' => $informasiRow?->kelas_workshop ?? [],
            'alumni' => $informasiRow?->alumni ?? [],
            'testimoni' => $informasiRow?->testimoni ?? [],
            'kerjasama' => $informasiRow?->kerjasama ?? [],
            'faq' => $informasiRow?->faq ?? [],
        ];
    }

    // 1. Halaman Sambutan Kepala
    public function sambutan()
    {
        return view('profil.sambutan', $this->getCommonData());
    }

    // 2. Halaman Tentang Kami
    public function tentangKami()
    {
        return view('profil.tentang-kami', $this->getCommonData());
    }

    public function ppid()
    {
        return view('profil.ppid', $this->getCommonData());
    }

    public function pejabat()
    {
        return view('profil.pejabat', $this->getCommonData());
    }

    // 3. Halaman Visi Misi
    public function visiMisi()
    {
        return view('profil.visi-misi', $this->getCommonData());
    }

    // 4. Halaman Tugas & Fungsi
    public function tugasFungsi()
    {
        return view('profil.tugas-fungsi', $this->getCommonData());
    }

    // 5. Halaman Struktur Organisasi
    public function strukturOrganisasi()
    {
        return view('profil.struktur', $this->getCommonData());
    }

    public function kejuruan()
    {
        // Sekarang otomatis membawa data 'kejuruan' dari getCommonData()
        return view('informasi.kejuruan', $this->getCommonData());
    }

    public function fasilitas()
    {
        return view('informasi.fasilitas', $this->getCommonData());
    }

    public function workshop()
    {
        return view('informasi.workshop', $this->getCommonData());
    }

    public function alumni()
    {
        return view('informasi.alumni', $this->getCommonData());
    }

    public function testimoni()
    {
        return view('informasi.testimoni', $this->getCommonData());
    }

    public function berkala()
    {
        $data = $this->getCommonData();

        // Tambahkan ::query() sebelum ->where()
        $data['dokumen'] = InformasiPublik::query()
            ->where('kategori', 'berkala')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('informasi-publik.berkala', $data);
    }

    public function sertaMerta()
    {
        $data = $this->getCommonData();

        // Tambahkan ::query() sebelum ->where()
        $data['dokumen'] = InformasiPublik::query()
            ->where('kategori', 'serta_merta')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('informasi-publik.serta-merta', $data);
    }

    public function setiapSaat()
    {
        $data = $this->getCommonData();

        // Tambahkan ::query() sebelum ->where()
        $data['dokumen'] = InformasiPublik::query()
            ->where('kategori', 'setiap_saat')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('informasi-publik.setiap-saat', $data);
    }

    public function maklumat()
    {
        return view('pelayanan-publik.maklumat', $this->getCommonData());
    }

    public function standar()
    {
        return view('pelayanan-publik.standar', $this->getCommonData());
    }

    public function alur()
    {
        return view('pelayanan-publik.alur', $this->getCommonData());
    }

    public function surveyKepuasan()
    {
        return view('pelayanan-publik.survey-kepuasan', $this->getCommonData());
    }

    public function surveyKebutuhan()
    {
        return view('pelayanan-publik.survey-kebutuhan', $this->getCommonData());
    }

    public function surveyKebekerjaan()
    {
        return view('pelayanan-publik.survey-kebekerjaan', $this->getCommonData());
    }

    public function indeksKepuasan()
    {
        return view('pelayanan-publik.indeks-kepuasan', $this->getCommonData());
    }

    public function berita()
    {
        $data = $this->getCommonData();

        // Ambil data yang tipenya 'berita' urut terbaru
        $data['berita_list'] = \App\Models\BeritaDanGaleri::query()
            ->where('jenis', 'berita')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('berita.index', $data);
    }

    public function detailBerita($id)
    {
        $data = $this->getCommonData();

        // 1. Cari berita utama berdasarkan ID
        $berita = \App\Models\BeritaDanGaleri::query()
            ->where('jenis', 'berita')
            ->findOrFail($id);
        $data['berita'] = $berita;

        // 2. Ambil Berita Sebelumnya (Paling dekat berdasarkan tanggal dibuat)
        $data['prevBerita'] = \App\Models\BeritaDanGaleri::query()
            ->where('jenis', 'berita')
            ->where('created_at', '<', $berita->created_at)
            ->latest()
            ->first();

        // 3. Ambil Berita Selanjutnya (Paling dekat setelah tanggal dibuat)
        $data['nextBerita'] = \App\Models\BeritaDanGaleri::query()
            ->where('jenis', 'berita')
            ->where('created_at', '>', $berita->created_at)
            ->oldest()
            ->first();

        // 4. Ambil Berita Terkait (3 Berita terbaru lainnya untuk rekomendasi baca)
        $data['beritaTerkait'] = \App\Models\BeritaDanGaleri::query()
            ->where('jenis', 'berita')
            ->where('id', '!=', $berita->id) // Jangan memunculkan berita yang sama
            ->latest()
            ->limit(3)
            ->get();

        return view('berita.show', $data);
    }

    public function galeri()
    {
        $data = $this->getCommonData();

        // Ambil data yang tipenya 'galeri' urut terbaru
        $data['galeri_list'] = \App\Models\BeritaDanGaleri::query()
            ->where('jenis', 'galeri')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('berita.galeri', $data);
    }

    public function jdih()
    {
        $data = $this->getCommonData();

        // Ambil semua data JDIH yang berlaku
        $data['jdih_list'] = \App\Models\Jdih::query()
            ->where('status_peraturan', 'berlaku')
            ->latest()
            ->get();

        return view('jdih.index', $data);
    }
}
