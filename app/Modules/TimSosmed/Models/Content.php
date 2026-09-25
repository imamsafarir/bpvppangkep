<?php

namespace App\Modules\TimSosmed\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Content extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    /**
     * Saat Content dihapus, hapus semua file media dari storage.
     * Spatie InteractsWithMedia sudah handle ini, method ini sebagai pengaman eksplisit.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (self $content) {
            // Hapus semua media beserta file fisiknya dari disk
            $content->media()->get()->each(fn($media) => $media->delete());
        });
    }

    // =========================
    // SPATIE MEDIA LIBRARY
    // =========================

    /**
     * Daftarkan koleksi media:
     * - 'bahan'   : bahan mentah dari Planner (gambar/video, bisa lebih dari satu)
     * - 'editing' : hasil editing dari Editor (gambar/video, bisa lebih dari satu)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('bahan')
            ->useDisk('public');

        $this->addMediaCollection('editing')
            ->useDisk('public');
    }

    /**
     * Konversi otomatis:
     * - Gambar → AVIF (untuk preview, hemat bandwidth)
     * - Download selalu menggunakan file asli (original)
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Hanya proses file gambar (bukan video)
        $this->addMediaConversion('avif-preview')
            ->format('avif')
            ->width(1280)
            ->performOnCollections('bahan', 'editing')
            ->nonQueued(); // Queued via Job terpisah untuk video AV1
    }

    // RELASI PENANGGUNG JAWAB
    // =========================

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    public function planner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // =========================
    // RELASI KONTEN
    // =========================

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ContentRead::class);
    }

    /**
     * Hitung jumlah komentar belum dibaca untuk user tertentu
     */
    public function getUnreadCommentsCount(?int $userId = null): int
    {
        $userId = $userId ?? \Illuminate\Support\Facades\Auth::id();
        if (! $userId) {
            return 0;
        }

        $comments = $this->relationLoaded('comments') ? $this->comments : $this->comments()->get();
        if ($comments->isEmpty()) {
            return 0;
        }

        $userRead = $this->relationLoaded('reads')
            ? $this->reads->firstWhere('user_id', $userId)
            : $this->reads()->where('user_id', $userId)->first();

        $lastReadAt = $userRead?->last_read_at ? \Illuminate\Support\Carbon::parse($userRead->last_read_at) : null;

        if ($lastReadAt) {
            return $comments->where('user_id', '!=', $userId)
                ->filter(fn($c) => $c->created_at > $lastReadAt)
                ->count();
        }

        return $comments->where('user_id', '!=', $userId)->count();
    }

    /**
     * Cek apakah terdapat komentar belum dibaca untuk user tertentu
     */
    public function hasUnreadComments(?int $userId = null): bool
    {
        return $this->getUnreadCommentsCount($userId) > 0;
    }
}
