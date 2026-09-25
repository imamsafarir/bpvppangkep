<?php

namespace App\Modules\TimSosmed\Observers;

use App\Modules\TimSosmed\Jobs\CompressMediaToAv1Job;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Observer untuk media yang diupload ke koleksi TimSosmed.
 * Dispatch job kompresi AV1 setelah video tersimpan.
 */
class ContentMediaObserver
{
    public function created(Media $media): void
    {
        // Hanya proses koleksi milik Content (bahan & editing)
        if (! in_array($media->collection_name, ['bahan', 'editing'])) {
            return;
        }

        // Hanya proses video
        if (str_starts_with($media->mime_type, 'video/')) {
            CompressMediaToAv1Job::dispatch($media->id)
                ->onQueue('media');
        }
    }
}
