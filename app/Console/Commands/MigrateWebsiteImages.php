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
 * 3. Compress dan konversi gambar ke .avif sesungguhnya (termasuk update nama ekstensi)
 * 4. Pindahkan gambar attachment di dalam konten RichEditor (berita, profil sambutan, dll) dan perbarui link HTML di database
 * 5. Update seluruh path di database agar akurat dengan storage baru
 */
class MigrateWebsiteImages extends Command
{
    protected $signature   = 'website:migrate-images';
    protected $description = 'Migrasi gambar dan dokumen lama ke folder website/ dan konversi gambar ke AVIF';

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

    private int $moved      = 0;
    private int $converted  = 0;
    private int $skipped    = 0;
    private int $errors     = 0;

    public function handle(): int
    {
        $this->info('🚀 Memulai sinkronisasi seluruh media & file Website ke folder website/ ...');

        $this->migrateBeritaGaleri();
        $this->migrateProfil();
        $this->migrateInformasi();
        $this->migratePelayanan();
        $this->migrateWebsiteSettings();
        $this->migrateInformasiPublik();
        $this->migrateJdih();

        $this->newLine();
        $this->info('✅ Sinkronisasi Website Selesai!');
        $this->table(
            ['Dipindahkan / Disesuaikan', 'Dikonversi ke AVIF', 'Dilewati (Sudah Siap)', 'File Hilang / Error'],
            [[$this->moved, $this->converted, $this->skipped, $this->errors]]
        );

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    //  BERITA & GALERI
    // ──────────────────────────────────────────────

    private function migrateBeritaGaleri(): void
    {
        $this->line('📰 Memproses Berita & Galeri (termasuk isi konten & attachment)...');

        BeritaDanGaleri::chunk(50, function ($rows) {
            foreach ($rows as $row) {
                $changed = false;
                $updateData = [];

                // 1. file_foto (sampul berita atau kumpulan foto galeri)
                $fotos = (array) ($row->file_foto ?? []);
                $newFotos = [];

                foreach ($fotos as $path) {
                    $newPath = $this->migrateAndConvertFile($path, true);
                    $newFotos[] = $newPath;
                    if ($newPath !== $path) {
                        $changed = true;
                    }
                }

                if ($newFotos !== $fotos) {
                    $updateData['file_foto'] = json_encode(array_values($newFotos));
                    $changed = true;
                }

                // 2. Attachment di dalam konten HTML berita (rich editor)
                if (! empty($row->konten_berita)) {
                    $newHtml = $this->migrateHtmlAttachments($row->konten_berita, 'website/berita/konten');
                    if ($newHtml !== $row->konten_berita) {
                        $updateData['konten_berita'] = $newHtml;
                        $changed = true;
                    }
                }

                if ($changed && ! empty($updateData)) {
                    DB::table('berita_dan_galeris')
                        ->where('id', $row->id)
                        ->update($updateData);
                }
            }
        });
    }

    // ──────────────────────────────────────────────
    //  PROFIL
    // ──────────────────────────────────────────────

    private function migrateProfil(): void
    {
        $this->line('👤 Memproses Profil Balai & Sambutan...');

        $profil = Profil::first();
        if (! $profil) {
            return;
        }

        $changed = false;
        $data    = [];

        // chief photo
        $newChief = $this->migrateAndConvertFile($profil->chief_photo_path, true);
        if ($newChief !== $profil->chief_photo_path) {
            $data['chief_photo_path'] = $newChief;
            $changed = true;
        }

        // struktur organisasi
        $newStruktur = $this->migrateAndConvertFile($profil->struktur_organisasi, true);
        if ($newStruktur !== $profil->struktur_organisasi) {
            $data['struktur_organisasi'] = $newStruktur;
            $changed = true;
        }

        // sambutan kepala balai (HTML content)
        if (! empty($profil->sambutan_kepala)) {
            $newSambutan = $this->migrateHtmlAttachments($profil->sambutan_kepala, 'website/profil/sambutan');
            if ($newSambutan !== $profil->sambutan_kepala) {
                $data['sambutan_kepala'] = $newSambutan;
                $changed = true;
            }
        }

        // pejabat struktural (JSON repeater)
        $pejabat    = (array) ($profil->pejabat_struktural ?? []);
        $newPejabat = [];
        $pejChanged = false;

        foreach ($pejabat as $item) {
            if (! empty($item['foto'])) {
                $newFoto = $this->migrateAndConvertFile($item['foto'], true);
                if ($newFoto !== $item['foto']) {
                    $item['foto'] = $newFoto;
                    $pejChanged   = true;
                }
            }
            $newPejabat[] = $item;
        }

        if ($pejChanged) {
            $data['pejabat_struktural'] = json_encode(array_values($newPejabat));
            $changed = true;
        }

        if ($changed) {
            DB::table('profils')
                ->where('id', $profil->id)
                ->update($data);
        }
    }

    // ──────────────────────────────────────────────
    //  INFORMASI
    // ──────────────────────────────────────────────

    private function migrateInformasi(): void
    {
        $this->line('📚 Memproses Informasi (Kejuruan, Fasilitas, Workshop, Testimoni)...');

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
            $items        = (array) ($info->$field ?? []);
            $newItems     = [];
            $fieldChanged = false;

            foreach ($items as $item) {
                if (! empty($item[$imageKey])) {
                    $newPath = $this->migrateAndConvertFile($item[$imageKey], true);
                    if ($newPath !== $item[$imageKey]) {
                        $item[$imageKey] = $newPath;
                        $fieldChanged    = true;
                    }
                }
                $newItems[] = $item;
            }

            if ($fieldChanged) {
                $data[$field] = json_encode(array_values($newItems));
                $changed      = true;
            }
        }

        if ($changed) {
            DB::table('informasis')
                ->where('id', $info->id)
                ->update($data);
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
        $alur = (array) ($pelayanan->alur_pelayanan ?? []);
        $newAlur = [];
        $alurChanged = false;

        foreach ($alur as $item) {
            if (! empty($item['foto_alur'])) {
                $newPath = $this->migrateAndConvertFile($item['foto_alur'], true);
                if ($newPath !== $item['foto_alur']) {
                    $item['foto_alur'] = $newPath;
                    $alurChanged       = true;
                }
            }
            $newAlur[] = $item;
        }

        if ($alurChanged) {
            $dbUpdates['alur_pelayanan'] = json_encode(array_values($newAlur));
        }

        // 2. Maklumat pelayanan (dokumen/gambar)
        $maklumat = (array) ($pelayanan->maklumat_pelayanan ?? []);
        $newMaklumat = [];
        $maklumatChanged = false;

        foreach ($maklumat as $item) {
            if (! empty($item['file_maklumat'])) {
                $newPath = $this->migrateAndConvertFile($item['file_maklumat'], false);
                if ($newPath !== $item['file_maklumat']) {
                    $item['file_maklumat'] = $newPath;
                    $maklumatChanged       = true;
                }
            }
            $newMaklumat[] = $item;
        }

        if ($maklumatChanged) {
            $dbUpdates['maklumat_pelayanan'] = json_encode(array_values($newMaklumat));
        }

        // 3. Standar pelayanan (dokumen/gambar)
        $standar = (array) ($pelayanan->standar_pelayanan ?? []);
        $newStandar = [];
        $standarChanged = false;

        foreach ($standar as $item) {
            if (! empty($item['file_standar'])) {
                $newPath = $this->migrateAndConvertFile($item['file_standar'], false);
                if ($newPath !== $item['file_standar']) {
                    $item['file_standar'] = $newPath;
                    $standarChanged       = true;
                }
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
        $this->line('⚙️ Memproses Pengaturan Website (Logo, Favicon, Sliders, Popup)...');

        $setting = WebsiteSetting::first();
        if (! $setting) {
            return;
        }

        $data    = [];
        $changed = false;

        foreach (['logo_path', 'favicon_path', 'popup_image_path'] as $field) {
            if (! empty($setting->$field)) {
                $shouldConvert = ($field !== 'favicon_path');
                $newPath = $this->migrateAndConvertFile($setting->$field, $shouldConvert);
                if ($newPath !== $setting->$field) {
                    $data[$field] = $newPath;
                    $changed      = true;
                }
            }
        }

        // Sliders (array)
        $sliders    = (array) ($setting->sliders ?? []);
        $newSliders = [];
        $sChanged   = false;

        foreach ($sliders as $slider) {
            $newPath = $this->migrateAndConvertFile($slider, true);
            $newSliders[] = $newPath;
            if ($newPath !== $slider) {
                $sChanged = true;
            }
        }

        if ($sChanged) {
            $data['sliders'] = json_encode(array_values($newSliders));
            $changed         = true;
        }

        if ($changed) {
            DB::table('website_settings')
                ->where('id', $setting->id)
                ->update($data);
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

                $newPath = $this->migrateAndConvertFile($row->file_path, false);
                if ($newPath !== $row->file_path) {
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

                $newPath = $this->migrateAndConvertFile($row->file_path, false);
                if ($newPath !== $row->file_path) {
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
     * Memindahkan file ke folder website/ bila belum di sana,
     * lalu mengonversinya ke AVIF (jika format gambar dan $shouldConvert = true).
     *
     * @param  string|null  $relativePath
     * @param  bool         $shouldConvert
     * @return string|null  Relative path akhir (bisa berganti nama jadi .avif)
     */
    private function migrateAndConvertFile(?string $relativePath, bool $shouldConvert = true): ?string
    {
        if (empty($relativePath)) {
            $this->skipped++;
            return null;
        }

        $disk = Storage::disk('public');
        $cleanPath = str_replace('\\', '/', $relativePath);

        // Tentukan path tujuan di dalam folder website/
        $targetPath = $cleanPath;
        if (! str_starts_with($cleanPath, 'website/') && ! str_starts_with($cleanPath, 'timsosmed/')) {
            $mapped = false;
            foreach (self::DIR_MAP as $oldDir => $newDir) {
                if (str_starts_with($cleanPath, $oldDir . '/')) {
                    $subPath    = substr($cleanPath, strlen($oldDir . '/'));
                    $targetPath = $newDir . '/' . $subPath;
                    $mapped     = true;
                    break;
                }
            }
            if (! $mapped) {
                $targetPath = 'website/' . ltrim($cleanPath, '/');
            }
        }

        // Jika file lama belum ada di targetPath tetapi ada di cleanPath, pindahkan
        if ($targetPath !== $cleanPath && $disk->exists($cleanPath)) {
            $targetDir = dirname($targetPath);
            if (! $disk->directoryExists($targetDir)) {
                $disk->makeDirectory($targetDir);
            }
            $disk->put($targetPath, $disk->get($cleanPath));
            $disk->delete($cleanPath);
            $this->moved++;
            $this->line("  ✓ Pindah: {$cleanPath} → {$targetPath}");
        } elseif (! $disk->exists($targetPath)) {
            // Cek kemungkinan file sudah berakhiran .avif
            $avifGuess = pathinfo($targetPath, PATHINFO_DIRNAME) . '/' . pathinfo($targetPath, PATHINFO_FILENAME) . '.avif';
            if ($disk->exists($avifGuess)) {
                $this->skipped++;
                return $avifGuess;
            }

            $this->warn("  ⚠ File tidak ditemukan di storage: {$cleanPath}");
            $this->errors++;
            return $cleanPath;
        }

        // Sekarang file sudah berada di $targetPath. Jika gambar & ingin dikonversi ke AVIF:
        $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $skipConversion = in_array($ext, ['ico', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'mp4', 'mov', 'webm']);

        if ($shouldConvert && ! $skipConversion) {
            $finalPath = ImageCompressor::convertToAvifPublic($targetPath, 75);
            if ($finalPath !== $targetPath) {
                $this->converted++;
                $this->line("  ✨ Konversi AVIF: {$targetPath} → {$finalPath}");
                return $finalPath;
            } elseif ($ext === 'avif') {
                $this->skipped++;
                return $targetPath;
            }
        }

        return $targetPath;
    }

    /**
     * Memindai tag <img> / link di dalam konten RichEditor HTML,
     * memindahkan filenya ke folder target website/ jika masih di folder lama,
     * dan mengonversi gambar ke AVIF sekaligus memperbarui URL di teks HTML.
     */
    private function migrateHtmlAttachments(string $html, string $targetFolder): string
    {
        $disk = Storage::disk('public');

        return preg_replace_callback('/(src|href)=["\']([^"\']+)["\']/', function ($matches) use ($disk, $targetFolder) {
            $attr = $matches[1];
            $url  = $matches[2];

            // Hanya proses URL storage lokal
            $parsed = parse_url($url, PHP_URL_PATH);
            if (! $parsed || ! str_contains($parsed, '/storage/')) {
                return $matches[0];
            }

            // Ambil relative path setelah /storage/
            $relPath = substr($parsed, strpos($parsed, '/storage/') + 9);
            $relPath = ltrim(str_replace('\\', '/', $relPath), '/');

            if (empty($relPath)) {
                return $matches[0];
            }

            // Jalankan migrasi dan konversi
            $newRelPath = $this->migrateAndConvertFile($relPath, true);
            if ($newRelPath && $newRelPath !== $relPath) {
                $newUrl = asset('storage/' . $newRelPath);
                return "{$attr}=\"{$newUrl}\"";
            }

            return $matches[0];
        }, $html);
    }
}
