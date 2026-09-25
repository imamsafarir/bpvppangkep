<?php

namespace App\Modules\TimSosmed\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\TimSosmed\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class MediaDownloadController extends Controller
{
    /**
     * Download seluruh media dalam koleksi tertentu untuk 1 konten sebagai ZIP.
     */
    public function downloadContentCollection(Content $content, string $collection)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! in_array($collection, ['bahan', 'editing'])) {
            abort(404, 'Koleksi tidak valid.');
        }

        $mediaItems = $content->getMedia($collection);

        if ($mediaItems->isEmpty()) {
            return back()->with('error', 'Tidak ada media dalam koleksi ini.');
        }

        $cleanTitle = Str::slug($content->nama_kegiatan ?: 'konten-' . $content->id);
        $zipFileName = "{$cleanTitle}-{$collection}.zip";

        return $this->streamZipResponse($mediaItems, $zipFileName);
    }

    /**
     * Download multiple media berdasarkan list ID sebagai ZIP (dari Galeri).
     */
    public function downloadBatchZip(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = array_filter(array_map('intval', (array) $ids));

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu media untuk didownload.');
        }

        $mediaItems = Media::whereIn('id', $ids)
            ->where('model_type', Content::class)
            ->with('model')
            ->get();

        if ($mediaItems->isEmpty()) {
            return back()->with('error', 'Media yang dipilih tidak ditemukan.');
        }

        $zipFileName = 'galeri-media-' . now()->format('Ymd-His') . '.zip';

        return $this->streamZipResponse($mediaItems, $zipFileName);
    }

    /**
     * Helper untuk membuat ZIP file dan stream download.
     */
    protected function streamZipResponse($mediaItems, string $zipFileName)
    {
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip_');
        $zip = new ZipArchive();

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file arsip ZIP.');
        }

        $usedNames = [];
        $counter = 1;

        foreach ($mediaItems as $media) {
            $filePath = $media->getPath();
            if (! file_exists($filePath)) {
                continue;
            }

            $activityTitle = $media->model?->nama_kegiatan ? Str::slug($media->model->nama_kegiatan) : 'kegiatan';
            $originalName = $media->file_name;
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            // Susun nama rapi di dalam zip: [index]_[kegiatan]_[filename]
            $zipEntryName = sprintf('%02d_%s_%s.%s', $counter, $activityTitle, Str::slug($baseName), $extension);

            if (isset($usedNames[$zipEntryName])) {
                $zipEntryName = sprintf('%02d_%s_%s_%d.%s', $counter, $activityTitle, Str::slug($baseName), $usedNames[$zipEntryName]++, $extension);
            } else {
                $usedNames[$zipEntryName] = 1;
            }

            $zip->addFile($filePath, $zipEntryName);
            $counter++;
        }

        $zip->close();

        return response()->download($tempZipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}
