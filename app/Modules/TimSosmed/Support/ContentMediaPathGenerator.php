<?php

namespace App\Modules\TimSosmed\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Menyimpan file media dalam struktur folder yang rapi:
 *
 *   public/timsosmed/content/{id}/{collection}/          ← file asli
 *   public/timsosmed/content/{id}/{collection}/conversions/  ← AVIF, thumbnail, dll
 *
 * Contoh:
 *   public/timsosmed/content/42/bahan/foto-kegiatan.jpg
 *   public/timsosmed/content/42/bahan/conversions/foto-kegiatan-avif-preview.avif
 *   public/timsosmed/content/42/editing/video-final.mp4
 */
class ContentMediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return "timsosmed/content/{$media->model_id}/{$media->collection_name}/";
    }

    public function getPathForConversions(Media $media): string
    {
        return "timsosmed/content/{$media->model_id}/{$media->collection_name}/conversions/";
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return "timsosmed/content/{$media->model_id}/{$media->collection_name}/responsive/";
    }
}
