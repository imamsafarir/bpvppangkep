<?php

namespace App\Modules\Website\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Modules\Website\Models\Informasi;
use App\Modules\Website\Models\InformasiPublik;
use App\Modules\Website\Models\BeritaDanGaleri;
use App\Modules\Website\Models\PelayananPublik;
use App\Modules\Website\Models\Jdih;
use Illuminate\Support\Facades\Cache;
use ZipArchive;

class ProfilController
{
    private function getCommonData()
    {
        return Cache::remember('website_common_data', 600, function () {
            $informasiRow = Informasi::query()->first();

            return [
                'profil'    => DB::table('profils')->first(),
                'settings'  => DB::table('website_settings')->first(),
                'pelayanan' => PelayananPublik::query()->first(),
                'kejuruan'  => $informasiRow?->kejuruan ?? [],
                'fasilitas' => $informasiRow?->gedung_fasilitas ?? [],
                'workshop'  => $informasiRow?->kelas_workshop ?? [],
                'alumni'    => $informasiRow?->alumni ?? [],
                'testimoni' => $informasiRow?->testimoni ?? [],
                'kerjasama' => $informasiRow?->kerjasama ?? [],
                'faq'       => $informasiRow?->faq ?? [],
            ];
        });
    }

    public function sambutan()
    {
        return view('website::profil.sambutan', $this->getCommonData());
    }

    public function tentangKami()
    {
        return view('website::profil.tentang-kami', $this->getCommonData());
    }

    public function ppid()
    {
        return view('website::profil.ppid', $this->getCommonData());
    }

    public function pejabat()
    {
        return view('website::profil.pejabat', $this->getCommonData());
    }

    public function visiMisi()
    {
        return view('website::profil.visi-misi', $this->getCommonData());
    }

    public function tugasFungsi()
    {
        return view('website::profil.tugas-fungsi', $this->getCommonData());
    }

    public function strukturOrganisasi()
    {
        return view('website::profil.struktur', $this->getCommonData());
    }

    public function kejuruan()
    {
        return view('website::informasi.kejuruan', $this->getCommonData());
    }

    public function fasilitas()
    {
        return view('website::informasi.fasilitas', $this->getCommonData());
    }

    public function workshop()
    {
        return view('website::informasi.workshop', $this->getCommonData());
    }

    public function alumni()
    {
        return view('website::informasi.alumni', $this->getCommonData());
    }

    public function testimoni()
    {
        return view('website::informasi.testimoni', $this->getCommonData());
    }

    public function berkala()
    {
        $data = $this->getCommonData();
        $data['dokumen'] = InformasiPublik::query()->where('kategori', 'berkala')->orderBy('created_at', 'desc')->get();
        return view('website::informasi-publik.berkala', $data);
    }

    public function sertaMerta()
    {
        $data = $this->getCommonData();
        $data['dokumen'] = InformasiPublik::query()->where('kategori', 'serta_merta')->orderBy('created_at', 'desc')->get();
        return view('website::informasi-publik.serta-merta', $data);
    }

    public function setiapSaat()
    {
        $data = $this->getCommonData();
        $data['dokumen'] = InformasiPublik::query()->where('kategori', 'setiap_saat')->orderBy('created_at', 'desc')->get();
        return view('website::informasi-publik.setiap-saat', $data);
    }

    public function maklumat()
    {
        return view('website::pelayanan-publik.maklumat', $this->getCommonData());
    }

    public function standar()
    {
        return view('website::pelayanan-publik.standar', $this->getCommonData());
    }

    public function alur()
    {
        return view('website::pelayanan-publik.alur', $this->getCommonData());
    }

    public function surveyKepuasan()
    {
        return view('website::pelayanan-publik.survey-kepuasan', $this->getCommonData());
    }

    public function surveyKebutuhan()
    {
        return view('website::pelayanan-publik.survey-kebutuhan', $this->getCommonData());
    }

    public function surveyKebekerjaan()
    {
        return view('website::pelayanan-publik.survey-kebekerjaan', $this->getCommonData());
    }

    public function indeksKepuasan()
    {
        return view('website::pelayanan-publik.indeks-kepuasan', $this->getCommonData());
    }

    public function berita()
    {
        $data = $this->getCommonData();
        $data['berita_list'] = BeritaDanGaleri::query()->where('jenis', 'berita')->orderBy('created_at', 'desc')->get();
        return view('website::berita.index', $data);
    }

    public function detailBerita($id)
    {
        $data   = $this->getCommonData();
        $berita = BeritaDanGaleri::query()->where('jenis', 'berita')->findOrFail($id);

        $data['berita']       = $berita;
        $data['prevBerita']   = BeritaDanGaleri::query()->where('jenis', 'berita')->where('created_at', '<', $berita->created_at)->latest()->first();
        $data['nextBerita']   = BeritaDanGaleri::query()->where('jenis', 'berita')->where('created_at', '>', $berita->created_at)->oldest()->first();
        $data['beritaTerkait'] = BeritaDanGaleri::query()->where('jenis', 'berita')->where('id', '!=', $berita->id)->latest()->limit(3)->get();

        return view('website::berita.show', $data);
    }

    public function galeri()
    {
        $data = $this->getCommonData();
        $data['galeri_list'] = BeritaDanGaleri::query()->where('jenis', 'galeri')->orderBy('created_at', 'desc')->get();
        return view('website::berita.galeri', $data);
    }

    public function jdih()
    {
        $data = $this->getCommonData();
        $data['jdih_list'] = Jdih::query()->where('status_peraturan', 'berlaku')->latest()->get();
        return view('website::jdih.index', $data);
    }

    public function download($id)
    {
        $galeri = BeritaDanGaleri::findOrFail($id);
        $files  = is_array($galeri->file_foto) ? $galeri->file_foto : (json_decode($galeri->file_foto, true) ?? []);

        if (empty($files)) {
            return back()->with('error', 'Tidak ada foto dalam galeri ini.');
        }

        $zipFileName = Str::slug($galeri->keterangan_galeri ?? 'galeri-kegiatan') . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    $zip->addFile(Storage::disk('public')->path($file), basename($file));
                }
            }
            $zip->close();
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
