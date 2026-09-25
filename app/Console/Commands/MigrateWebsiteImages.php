<?php

namespace App\Console\Commands;

use App\Modules\Website\Models\BeritaDanGaleri;
use App\Modules\Website\Models\Informasi;
use App\Modules\Website\Models\InformasiPublik;
use App\Modules\Website\Models\Jdih;
use App\Modules\Website\Models\PelayananPublik;
use App\Modules\Website\Models\Profil;
use App\Modules\Website\Models\WebsiteSetting;
use App\Services\ImageCompressor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Artisan command: php artisan website:migrate-images
 *
 * Tugas:
 * 1. Scan semua record di database yang menyimpan path gambar/file lama (tanpa prefix "website/")
 * 2. Pindahkan file fisiknya ke path baru (dengan prefix "website/")
 * 3. Compress ke AVIF jika berupa gambar
 * 4. Update path di database agar sesuai lokasi baru
 */
class MigrateWebsiteImages extends Command
{
    protected $signature   = 'website:migrate-images';
    protected $description = 'Migrasi gambar dan dokumen lama ke folder website/ dan kompres gambar ke AVIF';

    /** Mapping lama → baru untuk direktori sederhana */
    private const DIR_MAP = [
        'berita/sampul'          => 'website/berita/sampul',
        'berita/konten'          => 'website/berita/konten',
        'galeri/foto'            => 'website/galeri/foto',
        'profil/chief'           => 'website/profil/chief',
        'profil/struktur'        => 'website/profil/struktur',
        'profil/pejabat'         => 'website/profil/pejabat',
        'profil/sambutan'        => 'website/profil/sambutan',
        'informasi/kejuruan'     => 'website/informasi/kejuruan',
        'informasi/fasilitas'    => 'website/informasi/fasilitas',
        'informasi/workshop'     => 'website/informasi/workshop',
        'informasi/alumni'       => 'website/informasi/alumni',
        'informasi/testimoni'    => 'website/informasi/testimoni',
        'informasi/kerjasama'    => 'website/informasi/kerjasama',
        'pelayanan/alur'         => 'website/pelayanan/alur',
        'pelayanan/maklumat'     => 'website/pelayanan/maklumat',
        'pelayanan/standar'      => 'website/pelayanan/standar',
        'settings/branding'      => 'website/settings/branding',
        'settings/sliders'       => 'website/settings/sliders',
        'settings/popup'         => 'website/settings/popup',
        'informasi-publik/berkala'      => 'website/informasi-publik/berkala',
        'informasi-publik/serta_merta'  => 'website/informasi-publik/serta_merta',
        'informasi-publik/setiap_saat'  => 'website/informasi-publik/setiap_saat',
        'informasi-publik'              => 'website/informasi-publik',
        'jdih/dokumen'                  => 'website/jdih/dokumen',
    ];

    private int $moved     = 0;
    private int $compressed = 0;
    private int $skipped   = 0;
    private int $errors    = 0;

    public function handle(): int
    {
        $this->info('🚀 Memulai migrasi gambar & dokumen Website ke folder website/ ...');

        $this->migrateBeritaGaleri();
        $this->migrateProfil();
        $this->migrateInformasi();
        $this->migratePelayanan();
        $this->migrateWebsiteSettings();
        $this->migrateInformasiPublik();
        $this->migrateJdih();

        $this->newLine();
        $this->info("✅ Selesai!");
        $this->table(
            ['Dipindahkan', 'Dikompres (AVIF)', 'Dilewati', 'Error'],
            [[$this->moved, $this->compressed, $this->skipped, $this->errors]]
        );

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    //  BERITA & GALERI
    // ──────────────────────────────────────────────

    private function migrateBeritaGaleri(): void
    {
        $this->line('📰 Memproses Berita & Galeri...');

        BeritaDanGaleri::chunk(50, function ($rows) {
            foreach ($rows as $row) {
                $fotos = (array) ($row->file_foto ?? []);
                $changed = false;
                $newFotos = [];

                foreach ($fotos as $path) {
                    $newPath = $this->migrateFile($path, true);
                    $newFotos[] = $newPath ?? $path;
                    if ($newPath !== null) {
                        $changed = true;
                    }
                }

                if ($changed) {
                    DB::table('berita_dan_galeris')
                        ->where('id', $row->id)
                        ->update(['file_foto' => json_encode(array_values($newFotos))]);
                }
            }
        });
    }

    // ──────────────────────────────────────────────
    //  PROFIL
    // ──────────────────────────────────────────────

    private function migrateProfil(): void
    {
        $this->line('👤 Memproses Profil...');

        $profil = Profil::first();
        if (! $profil) {
            return;
        }

        $changed = false;
        $data    = [];

        // chief photo
        $newPath = $this->migrateFile($profil->chief_photo_path, true);
        if ($newPath !== null) {
            $data['chief_photo_path'] = $newPath;
            $changed = true;
        }

        // struktur organisasi
        $newPath = $this->migrateFile($profil->struktur_organisasi, true);
        if ($newPath !== null) {
            $data['struktur_organisasi'] = $newPath;
            $changed = true;
        }

        // pejabat struktural (JSON repeater)
        $pejabat    = (array) ($profil->pejabat_struktural ?? []);
        $newPejabat = [];
        $pejChanged = false;

        foreach ($pejabat as $item) {
            $newPath = $this->migrateFile($item['foto'] ?? null, true);
            if ($newPath !== null) {
                $item['foto'] = $newPath;
                $pejChanged   = true;
            }
            $newPejabat[] = $item;
        }

        if ($pejChanged) {
            $data['pejabat_struktural'] = $newPejabat;
            $changed = true;
        }

        if ($changed) {
            $dbData = [];
            foreach ($data as $key => $value) {
                $dbData[$key] = is_array($value) ? json_encode($value) : $value;
            }
            DB::table('profils')
                ->where('id', $profil->id)
                ->update($dbData);
        }
    }

    // ──────────────────────────────────────────────
    //  INFORMASI
    // ──────────────────────────────────────────────

    private function migrateInformasi(): void
    {
        $this->line('📚 Memproses Informasi...');

        $info = Informasi::first();
        if (! $info) {
            return;
        }

        $repeaterMap = [
            'kejuruan'         => 'foto_kejuruan',
            'gedung_fasilitas' => 'foto_fasilitas',
            'kelas_workshop'   => 'foto_ruangan',
            'alumni'           => 'foto_kegiatan_alumni',
            'testimoni'        => 'foto_alumni',
            'kerjasama'        => 'logo',
        ];

        $data    = [];
        $changed = false;

        foreach ($repeaterMap as $field => $imageKey) {
            $items      = (array) ($info->$field ?? []);
            $newItems   = [];
            $fieldChanged = false;

            foreach ($items as $item) {
                $newPath = $this->migrateFile($item[$imageKey] ?? null, true);
                if ($newPath !== null) {
                    $item[$imageKey] = $newPath;
                    $fieldChanged    = true;
                }
                $newItems[] = $item;
            }

            if ($fieldChanged) {
                $data[$field] = $newItems;
                $changed      = true;
            }
        }

        if ($changed) {
            $dbData = [];
            foreach ($data as $key => $value) {
                $dbData[$key] = is_array($value) ? json_encode(array_values($value)) : $value;
            }
            DB::table('informasis')
                ->where('id', $info->id)
                ->update($dbData);
        }
    }

    // ──────────────────────────────────────────────
    //  PELAYANAN PUBLIK
    // ──────────────────────────────────────────────

    private function migratePelayanan(): void
    {
        $this->line('🏛️ Memproses Pelayanan Publik...');

        $pelayanan = PelayananPublik::first();
        if (! $pelayanan) {
            return;
        }

        $dbUpdates = [];

        // 1. Alur pelayanan (foto/gambar)
        $alur    = (array) ($pelayanan->alur_pelayanan ?? []);
        $newAlur = [];
        $alurChanged = false;

        foreach ($alur as $item) {
            $newPath = $this->migrateFile($item['foto_alur'] ?? null, true);
            if ($newPath !== null) {
                $item['foto_alur'] = $newPath;
                $alurChanged       = true;
            }
            $newAlur[] = $item;
        }

        if ($alurChanged) {
            $dbUpdates['alur_pelayanan'] = json_encode(array_values($newAlur));
        }

        // 2. Maklumat pelayanan (dokumen/gambar)
        $maklumat    = (array) ($pelayanan->maklumat_pelayanan ?? []);
        $newMaklumat = [];
        $maklumatChanged = false;

        foreach ($maklumat as $item) {
            $newPath = $this->migrateFile($item['file_maklumat'] ?? null, true);
            if ($newPath !== null) {
                $item['file_maklumat'] = $newPath;
                $maklumatChanged       = true;
            }
            $newMaklumat[] = $item;
        }

        if ($maklumatChanged) {
            $dbUpdates['maklumat_pelayanan'] = json_encode(array_values($newMaklumat));
        }

        // 3. Standar pelayanan (dokumen/gambar)
        $standar    = (array) ($pelayanan->standar_pelayanan ?? []);
        $newStandar = [];
        $standarChanged = false;

        foreach ($standar as $item) {
            $newPath = $this->migrateFile($item['file_standar'] ?? null, true);
            if ($newPath !== null) {
                $item['file_standar'] = $newPath;
                $standarChanged       = true;
            }
            $newStandar[] = $item;
        }

        if ($standarChanged) {
            $dbUpdates['standar_pelayanan'] = json_encode(array_values($newStandar));
        }

        if (! empty($dbUpdates)) {
            DB::table('pelayanan_publiks')
                ->where('id', $pelayanan->id)
                ->update($dbUpdates);
        }
    }

    // ──────────────────────────────────────────────
    //  WEBSITE SETTINGS
    // ──────────────────────────────────────────────

    private function migrateWebsiteSettings(): void
    {
        $this->line('⚙️ Memproses Pengaturan Website...');

        $setting = WebsiteSetting::first();
        if (! $setting) {
            return;
        }

        $data    = [];
        $changed = false;

        foreach (['logo_path', 'favicon_path', 'popup_image_path'] as $field) {
            $newPath = $this->migrateFile($setting->$field, $field !== 'favicon_path');
            if ($newPath !== null) {
                $data[$field] = $newPath;
                $changed      = true;
            }
        }

        // Sliders (array)
        $sliders    = (array) ($setting->sliders ?? []);
        $newSliders = [];
        $sChanged   = false;

        foreach ($sliders as $slider) {
            $newPath = $this->migrateFile($slider, true);
            $newSliders[] = $newPath ?? $slider;
            if ($newPath !== null) {
                $sChanged = true;
            }
        }

        if ($sChanged) {
            $data['sliders'] = $newSliders;
            $changed         = true;
        }

        if ($changed) {
            $dbData = [];
            foreach ($data as $key => $value) {
                $dbData[$key] = is_array($value) ? json_encode($value) : $value;
            }
            DB::table('website_settings')
                ->where('id', $setting->id)
                ->update($dbData);
        }
    }

    // ──────────────────────────────────────────────
    //  INFORMASI PUBLIK (DOKUMEN/FILE)
    // ──────────────────────────────────────────────

    private function migrateInformasiPublik(): void
    {
        $this->line('📑 Memproses Informasi Publik (Dokumen)...');

        InformasiPublik::chunk(50, function ($rows) {
            foreach ($rows as $row) {
                if (empty($row->file_path)) {
                    continue;
                }

                $newPath = $this->migrateFile($row->file_path, false);
                if ($newPath !== null) {
                    DB::table('informasi_publiks')
                        ->where('id', $row->id)
                        ->update(['file_path' => $newPath]);
                }
            }
        });
    }

    // ──────────────────────────────────────────────
    //  JDIH (DOKUMEN REGULASI/PDF)
    // ──────────────────────────────────────────────

    private function migrateJdih(): void
    {
        $this->line('⚖️ Memproses JDIH (Produk Hukum)...');

        Jdih::chunk(50, function ($rows) {
            foreach ($rows as $row) {
                if (empty($row->file_path)) {
                    continue;
                }

                $newPath = $this->migrateFile($row->file_path, false);
                if ($newPath !== null) {
                    DB::table('jdihs')
                        ->where('id', $row->id)
                        ->update(['file_path' => $newPath]);
                }
            }
        });
    }

    // ──────────────────────────────────────────────
    //  CORE: Pindah & Compress file tunggal
    // ──────────────────────────────────────────────

    /**
     * Pindahkan satu file dari path lama ke path baru (berdasarkan DIR_MAP),
     * lalu compress ke AVIF jika berupa gambar.
     *
     * @param  string|null  $relativePath   Path relatif di disk 'public'
     * @param  bool         $shouldCompress Kompres ke AVIF jika true
     * @return string|null  Path baru jika berhasil dipindahkan, null jika tidak ada perubahan
     */
    private function migrateFile(?string $relativePath, bool $shouldCompress = true): ?string
    {
        if (empty($relativePath)) {
            $this->skipped++;
            return null;
        }

        // Normalisasi backslash jika ada
        $relativePath = str_replace('\\', '/', $relativePath);

        // Jika sudah di prefix website/ atau timsosmed/, tidak perlu dipindah
        if (str_starts_with($relativePath, 'website/') || str_starts_with($relativePath, 'timsosmed/')) {
            if ($shouldCompress) {
                $this->compressFile($relativePath);
            }
            $this->skipped++;
            return null;
        }

        // Tentukan prefix folder baru berdasarkan DIR_MAP
        $newRelativePath = null;
        foreach (self::DIR_MAP as $oldDir => $newDir) {
            if (str_starts_with($relativePath, $oldDir . '/')) {
                $subPath         = substr($relativePath, strlen($oldDir . '/'));
                $newRelativePath = $newDir . '/' . $subPath;
                break;
            }
        }

        if ($newRelativePath === null) {
            // Default fallback: taruh di bawah website/
            $newRelativePath = 'website/' . ltrim($relativePath, '/');
        }

        // Cek file sumber ada di disk
        if (! Storage::disk('public')->exists($relativePath)) {
            // Cek apakah file ternyata sudah berada di path baru
            if (Storage::disk('public')->exists($newRelativePath)) {
                if ($shouldCompress) {
                    $this->compressFile($newRelativePath);
                }
                $this->skipped++;
                return $newRelativePath;
            }

            $this->warn("  ⚠ File tidak ditemukan: {$relativePath}");
            $this->errors++;
            return null;
        }

        // Pastikan direktori tujuan ada
        $targetDir = dirname($newRelativePath);
        if (! Storage::disk('public')->directoryExists($targetDir)) {
            Storage::disk('public')->makeDirectory($targetDir);
        }

        // Salin file ke lokasi baru
        $contents = Storage::disk('public')->get($relativePath);
        Storage::disk('public')->put($newRelativePath, $contents);

        // Hapus file lama setelah berhasil disalin
        Storage::disk('public')->delete($relativePath);

        $this->moved++;
        $this->line("  ✓ Pindah: {$relativePath} → {$newRelativePath}");

        // Compress ke AVIF jika berupa gambar
        if ($shouldCompress) {
            $this->compressFile($newRelativePath);
        }

        return $newRelativePath;
    }

    private function compressFile(string $relativePath): void
    {
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        if ($ext === 'ico' || $ext === 'pdf' || $ext === 'avif' || $ext === 'doc' || $ext === 'docx') {
            return;
        }

        $ok = ImageCompressor::compressPublic($relativePath);
        if ($ok) {
            $this->compressed++;
        }
    }
}
