<x-filament-panels::page>
    @php
        $mediaItems = $this->getMediaItems();
    @endphp

    <style>
        /* ──────────────────────────────────────────────────────────────
           Galeri Media — Scoped styles
           ────────────────────────────────────────────────────────────── */
        .galeri-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px;
        }

        .galeri-filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .galeri-pill {
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
            background: var(--color-gray-100, #f3f4f6);
            color: var(--color-gray-600, #4b5563);
        }

        .dark .galeri-pill {
            background: var(--color-gray-800, #1f2937);
            color: var(--color-gray-300, #d1d5db);
        }

        .galeri-pill.active {
            background: var(--color-primary-600, #4f46e5);
            color: #fff;
            border-color: var(--color-primary-700, #4338ca);
        }

        .galeri-search {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .galeri-search input {
            width: 100%;
            padding: 8px 12px 8px 38px;
            border-radius: 10px;
            border: 1px solid var(--color-gray-200, #e5e7eb);
            background: var(--color-white, #ffffff);
            color: var(--color-gray-900, #111827);
            font-size: 14px;
            outline: none;
            transition: box-shadow 0.15s;
        }

        .dark .galeri-search input {
            background: var(--color-gray-900, #111827);
            border-color: var(--color-gray-700, #374151);
            color: var(--color-gray-100, #f3f4f6);
        }

        .galeri-search input:focus {
            box-shadow: 0 0 0 2px var(--color-primary-500, #6366f1);
            border-color: transparent;
        }

        .galeri-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-gray-400, #9ca3af);
            pointer-events: none;
        }

        /* Action Toolbar for Selection */
        .galeri-selection-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .dark .galeri-selection-toolbar {
            background: rgba(49, 46, 129, 0.35);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .galeri-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
            text-decoration: none;
        }

        .galeri-btn-primary {
            background: #4f46e5;
            color: #ffffff;
        }

        .galeri-btn-primary:hover {
            background: #4338ca;
        }

        .galeri-btn-secondary {
            background: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .dark .galeri-btn-secondary {
            background: #1f2937;
            color: #d1d5db;
            border-color: #4b5563;
        }

        /* Grid */
        .galeri-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        @media (min-width: 640px) {
            .galeri-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 768px) {
            .galeri-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .galeri-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        /* Card */
        .galeri-card {
            border-radius: 14px;
            overflow: hidden;
            background: var(--color-gray-100, #f3f4f6);
            border: 2px solid transparent;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07);
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            position: relative;
        }

        .dark .galeri-card {
            background: var(--color-gray-800, #1f2937);
        }

        .galeri-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .13);
            transform: translateY(-2px);
        }

        .galeri-card.is-selected {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 2px #818cf8;
        }

        /* Thumbnail */
        .galeri-thumb {
            aspect-ratio: 1/1;
            position: relative;
            overflow: hidden;
            background: var(--color-gray-200, #e5e7eb);
            cursor: pointer;
        }

        .dark .galeri-thumb {
            background: var(--color-gray-700, #374151);
        }

        .galeri-thumb img,
        .galeri-thumb video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.25s;
        }

        .galeri-card:hover .galeri-thumb img,
        .galeri-card:hover .galeri-thumb video {
            transform: scale(1.04);
        }

        /* Checkbox badge on card */
        .galeri-checkbox-wrapper {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
        }

        .galeri-checkbox {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 2px solid rgba(255, 255, 255, 0.9);
            background: rgba(0, 0, 0, 0.35);
            cursor: pointer;
            accent-color: #4f46e5;
        }

        /* Number Index Badge on Card */
        .galeri-index-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
            background: rgba(0, 0, 0, 0.7);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 9999px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /* Overlay */
        .galeri-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            opacity: 0;
            transition: background 0.2s, opacity 0.2s;
        }

        .galeri-card:hover .galeri-overlay {
            background: rgba(0, 0, 0, .42);
            opacity: 1;
        }

        .galeri-overlay-btn {
            background: rgba(255, 255, 255, .9);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #1f2937;
            transition: background 0.15s;
            text-decoration: none;
        }

        .galeri-overlay-btn:hover {
            background: #fff;
        }

        /* Play icon */
        .galeri-play {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .galeri-play-inner {
            background: rgba(0, 0, 0, .5);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .galeri-play-inner svg {
            color: #fff;
        }

        /* Info */
        .galeri-info {
            padding: 10px;
        }

        .galeri-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 9999px;
            margin-bottom: 4px;
        }

        .badge-bahan {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-editing {
            background: #d1fae5;
            color: #065f46;
        }

        .dark .badge-bahan {
            background: rgba(180, 83, 9, .3);
            color: #fde68a;
        }

        .dark .badge-editing {
            background: rgba(4, 120, 87, .3);
            color: #6ee7b7;
        }

        .galeri-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--color-gray-800, #1f2937);
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .dark .galeri-title {
            color: var(--color-gray-200, #e5e7eb);
        }

        .galeri-filename {
            font-size: 11px;
            color: var(--color-gray-500, #6b7280);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .galeri-meta {
            font-size: 10px;
            color: var(--color-gray-400, #9ca3af);
        }

        /* Empty state */
        .galeri-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 24px;
            color: var(--color-gray-400, #9ca3af);
            text-align: center;
        }

        .galeri-empty svg {
            opacity: 0.3;
            margin-bottom: 16px;
        }

        .galeri-empty p {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
        }

        .galeri-empty span {
            font-size: 13px;
        }

        /* Lightbox */
        .galeri-lightbox {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, .92);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .galeri-lightbox-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, .15);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            transition: background 0.15s;
        }

        .galeri-lightbox-close:hover {
            background: rgba(255, 255, 255, .25);
        }

        .galeri-lightbox img,
        .galeri-lightbox video {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, .6);
        }
    </style>

    <div x-data="{
        lightbox: null,
        lbSrc: '',
        lbType: '',
        selected: [],
        allIds: {{ json_encode($mediaItems->pluck('id')->toArray()) }},
        toggleAll() {
            if (this.selected.length === this.allIds.length) {
                this.selected = [];
            } else {
                this.selected = [...this.allIds];
            }
        },
        toggleItem(id) {
            const index = this.selected.indexOf(id);
            if (index > -1) {
                this.selected.splice(index, 1);
            } else {
                this.selected.push(id);
            }
        }
    }">

        {{-- ── FILTER BAR ── --}}
        <div class="galeri-filter-bar">
            <div class="galeri-filter-pills">
                @foreach (['semua' => '🗂️ Semua', 'bahan' => '📁 Bahan Mentah', 'editing' => '🎬 Hasil Editing'] as $key => $label)
                    <button wire:click="$set('filterKoleksi', '{{ $key }}')"
                        class="galeri-pill {{ $filterKoleksi === $key ? 'active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>

            <div class="galeri-search">
                <span class="galeri-search-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </span>
                <input wire:model.live.debounce.400ms="filterCari" type="text" placeholder="Cari nama kegiatan...">
            </div>
        </div>

        {{-- ── SELECTION TOOLBAR (Muncul jika ada item atau ingin select all) ── --}}
        @if (!$mediaItems->isEmpty())
            <div class="galeri-selection-toolbar">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <label
                        style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" class="galeri-checkbox"
                            :checked="selected.length > 0 && selected.length === allIds.length" @click="toggleAll()">
                        <span>Pilih Semua di Halaman Ini (<span x-text="selected.length"></span>/<span
                                x-text="allIds.length"></span>)</span>
                    </label>
                </div>

                <div>
                    <form method="POST" action="{{ route('timsosmed.gallery.download-zip') }}"
                        style="display: inline;">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="galeri-btn galeri-btn-primary" :disabled="selected.length === 0"
                            :style="selected.length === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            <span>Download ZIP Terpilih (<span x-text="selected.length"></span>)</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── GRID ── --}}
        @if ($mediaItems->isEmpty())
            <div class="galeri-empty">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5"
                    viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="3" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <path d="m21 15-5-5L5 21" />
                </svg>
                <p>Belum ada media</p>
                <span>Upload bahan mentah atau hasil editing pada form Konten.</span>
            </div>
        @else
            <div class="galeri-grid">
                @php
                    $startIndex = ($mediaItems->currentPage() - 1) * $mediaItems->perPage();
                @endphp
                @foreach ($mediaItems as $idx => $media)
                    @php
                        $itemNumber = $startIndex + $idx + 1;
                        $isVideo = str_starts_with($media->mime_type, 'video/');
                        $isImage = str_starts_with($media->mime_type, 'image/');
                        $activityName = $media->model?->nama_kegiatan ?? 'Tanpa Judul Kegiatan';
                        $preview =
                            $isImage && $media->hasGeneratedConversion('avif-preview')
                                ? $media->getUrl('avif-preview')
                                : $media->getUrl();
                        $original = $media->getUrl();
                        $lbType = $isVideo ? 'video' : 'image';
                        $sizeMb = number_format($media->size / 1048576, 1);
                        $date = $media->created_at->translatedFormat('d M Y');
                    @endphp

                    <div class="galeri-card" :class="selected.includes({{ $media->id }}) ? 'is-selected' : ''">
                        {{-- Checkbox Selection --}}
                        <div class="galeri-checkbox-wrapper" @click.stop>
                            <input type="checkbox" class="galeri-checkbox" :value="{{ $media->id }}"
                                :checked="selected.includes({{ $media->id }})"
                                @change="toggleItem({{ $media->id }})">
                        </div>

                        {{-- Thumbnail --}}
                        <div class="galeri-thumb"
                            @click="lbSrc='{{ $original }}'; lbType='{{ $lbType }}'; lightbox='{{ $media->id }}'">
                            @if ($isImage)
                                <img src="{{ $preview }}" alt="{{ e($activityName) }}" loading="lazy">
                            @elseif($isVideo)
                                <video src="{{ $original }}" muted preload="metadata" style="opacity:.75"></video>
                                <div class="galeri-play">
                                    <div class="galeri-play-inner">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 3l14 9-14 9V3z" />
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div
                                    style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:12px;gap:6px;color:#94a3b8">
                                    <svg width="36" height="36" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                    <span
                                        style="font-size:10px;word-break:break-all;text-align:center">{{ $media->file_name }}</span>
                                </div>
                            @endif

                            {{-- Hover Overlay --}}
                            <div class="galeri-overlay">
                                @if ($isImage || $isVideo)
                                    <button type="button" class="galeri-overlay-btn"
                                        @click.stop="lbSrc='{{ $original }}'; lbType='{{ $lbType }}'; lightbox='{{ $media->id }}'"
                                        title="Lihat">
                                        <svg width="16" height="16" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </button>
                                @endif
                                <a href="{{ $original }}" download="{{ $media->file_name }}"
                                    class="galeri-overlay-btn" title="Download file asli" @click.stop>
                                    <svg width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="7 10 12 15 17 10" />
                                        <line x1="12" y1="15" x2="12" y2="3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Info Judul Kegiatan --}}
                        <div class="galeri-info">
                            <span
                                class="galeri-badge {{ $media->collection_name === 'bahan' ? 'badge-bahan' : 'badge-editing' }}">
                                {{ $media->collection_name === 'bahan' ? '📁 Bahan' : '🎬 Editing' }}
                            </span>
                            <div class="galeri-title" title="{{ $activityName }}">
                                {{ $activityName }}
                            </div>
                            <div class="galeri-meta">{{ $date }} &bull; {{ $sizeMb }} MB</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div style="margin-top:20px">
                {{ $mediaItems->links() }}
            </div>
        @endif

        {{-- ── LIGHTBOX ── --}}
        <div class="galeri-lightbox" x-show="lightbox !== null" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click.self="lightbox = null" @keydown.escape.window="lightbox = null"
            style="display:none">
            <button class="galeri-lightbox-close" @click="lightbox = null">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <template x-if="lbType === 'image'">
                <img :src="lbSrc" alt="Preview">
            </template>
            <template x-if="lbType === 'video'">
                <video :src="lbSrc" controls autoplay></video>
            </template>
        </div>
    </div>
</x-filament-panels::page>
