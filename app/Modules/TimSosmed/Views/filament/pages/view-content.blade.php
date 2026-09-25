<x-filament-panels::page>
    {{-- =========================================================================
       VIEW CONTENT - EXECUTIVE DETAIL DASHBOARD
       100% SELF-CONTAINED CSS (LIGHT & DARK MODE READY, IFRAME COMPATIBLE)
       LAYOUT: DEDICATED FULL-HEIGHT CHAT SIDEBAR ON THE RIGHT (TOP TO BOTTOM)
       ========================================================================= --}}
    <style>
        :root {
            --vc-bg: #f8fafc;
            --vc-card-bg: #ffffff;
            --vc-card-border: #e2e8f0;
            --vc-text-main: #0f172a;
            --vc-text-muted: #64748b;
            --vc-primary: #4f46e5;
            --vc-primary-hover: #4338ca;
            --vc-radius: 14px;
        }

        .dark {
            --vc-bg: #0b0f19;
            --vc-card-bg: #1e293b;
            --vc-card-border: #334155;
            --vc-text-main: #f8fafc;
            --vc-text-muted: #94a3b8;
            --vc-primary: #6366f1;
            --vc-primary-hover: #4f46e5;
        }

        .vc-container {
            font-family: inherit;
            color: var(--vc-text-main);
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* --- 2-COLUMN MAIN LAYOUT (CHAT ON RIGHT FROM TOP TO BOTTOM) --- */
        .vc-layout-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 390px;
            gap: 20px;
            align-items: start;
            width: 100%;
        }

        @media (max-width: 1100px) {
            .vc-layout-grid {
                grid-template-columns: 1fr;
            }
        }

        .vc-col-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .vc-col-sidebar {
            min-width: 0;
            position: sticky;
            top: 16px;
        }

        @media (max-width: 1100px) {
            .vc-col-sidebar {
                position: static;
            }
        }

        /* --- RIGHT SIDEBAR CHAT (FULL HEIGHT TOP-TO-BOTTOM) --- */
        .vc-sidebar-chat-card {
            background-color: var(--vc-card-bg);
            border: 1px solid var(--vc-card-border);
            border-radius: var(--vc-radius);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 40px);
            max-height: calc(100vh - 40px);
            padding: 18px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .dark .vc-sidebar-chat-card {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.3);
        }

        .vc-chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--vc-card-border);
            flex-shrink: 0;
        }

        .vc-chat-title-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .vc-chat-main-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--vc-text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .vc-chat-sub {
            font-size: 11px;
            color: var(--vc-text-muted);
        }

        .vc-chat-body {
            flex: 1 1 0%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Flex Livewire chat inside full-height sidebar */
        .vc-chat-body .ts-comments-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
            gap: 10px;
            flex: 1;
        }

        .vc-chat-body .ts-comments-list {
            flex: 1 1 0% !important;
            max-height: none !important;
            min-height: 0 !important;
            overflow-y: auto !important;
        }

        .vc-chat-body .ts-input-section {
            flex-shrink: 0;
        }

        @media (max-width: 1100px) {
            .vc-sidebar-chat-card {
                height: auto;
                max-height: 650px;
            }

            .vc-chat-body .ts-comments-list {
                max-height: 420px !important;
            }
        }

        /* --- HERO BANNER --- */
        .vc-hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
            border: 1px solid var(--vc-card-border);
            border-radius: var(--vc-radius);
            padding: 22px 26px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        .dark .vc-hero-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.3);
        }

        .vc-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .vc-hero-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vc-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.3;
            letter-spacing: 0.02em;
            text-transform: capitalize;
        }

        .vc-badge-draft {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .vc-badge-menunggu_editor {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .vc-badge-revisi_editor,
        .vc-badge-revisi_planner {
            background: #ffe4e6;
            color: #e11d48;
            border: 1px solid #fecdd3;
        }

        .vc-badge-siap_publish {
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        .vc-badge-selesai {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .dark .vc-badge-draft {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border-color: #475569;
        }

        .dark .vc-badge-menunggu_editor {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .dark .vc-badge-revisi_editor,
        .dark .vc-badge-revisi_planner {
            background: rgba(225, 29, 72, 0.15);
            color: #fb7185;
            border-color: rgba(225, 29, 72, 0.3);
        }

        .dark .vc-badge-siap_publish {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border-color: rgba(14, 165, 233, 0.3);
        }

        .dark .vc-badge-selesai {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .vc-type-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 9999px;
            letter-spacing: 0.05em;
        }

        .vc-type-final {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .vc-type-bahan {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .dark .vc-type-final {
            background: rgba(22, 163, 74, 0.2);
            color: #4ade80;
            border-color: rgba(22, 163, 74, 0.4);
        }

        .dark .vc-type-bahan {
            background: rgba(217, 119, 6, 0.2);
            color: #fcd34d;
            border-color: rgba(217, 119, 6, 0.4);
        }

        .vc-hero-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vc-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border: 1px solid transparent;
            line-height: 1.4;
        }

        .vc-btn-primary {
            background: #4f46e5;
            color: #ffffff !important;
        }

        .vc-btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .vc-btn-secondary {
            background: #ffffff;
            color: #334155 !important;
            border-color: #cbd5e1;
        }

        .vc-btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .dark .vc-btn-secondary {
            background: #1e293b;
            color: #f1f5f9 !important;
            border-color: #475569;
        }

        .dark .vc-btn-secondary:hover {
            background: #334155;
        }

        .vc-btn-success {
            background: #10b981;
            color: #ffffff !important;
        }

        .vc-btn-success:hover {
            background: #059669;
        }

        .vc-hero-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--vc-text-main);
            line-height: 1.3;
            letter-spacing: -0.02em;
            margin: 0 0 12px 0;
            word-break: break-word;
        }

        .vc-hero-meta-row {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--vc-text-muted);
            padding-top: 10px;
            border-top: 1px dashed var(--vc-card-border);
        }

        .vc-hero-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .vc-hero-platforms {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .vc-platform-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .dark .vc-platform-pill {
            background: #0f172a;
            border-color: #475569;
            color: #e2e8f0;
        }

        /* --- UNIFIED WORKFLOW STEPPER & PIC DIRECTORY --- */
        .vc-stepper-card {
            background-color: var(--vc-card-bg);
            border: 1px solid var(--vc-card-border);
            border-radius: var(--vc-radius);
            padding: 20px 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            box-sizing: border-box;
        }

        .vc-stepper-heading {
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--vc-text-muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vc-stepper-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            position: relative;
        }

        @media (max-width: 850px) {
            .vc-stepper-grid {
                grid-template-columns: 1fr;
            }
        }

        .vc-step-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            transition: all 0.15s ease;
        }

        .dark .vc-step-item {
            background: rgba(15, 23, 42, 0.6);
            border-color: #334155;
        }

        .vc-step-item.is-active {
            border-color: #6366f1;
            background: #f5f3ff;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .dark .vc-step-item.is-active {
            background: rgba(99, 102, 241, 0.15);
            border-color: #818cf8;
        }

        .vc-step-item.is-completed {
            border-color: #a7f3d0;
            background: #f0fdf4;
        }

        .dark .vc-step-item.is-completed {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .vc-step-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        .vc-step-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--vc-text-main);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .vc-step-state-tag {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 1.5px 6px;
            border-radius: 9999px;
            letter-spacing: 0.03em;
        }

        .vc-step-tag-active {
            background: #6366f1;
            color: #ffffff;
        }

        .vc-step-tag-done {
            background: #10b981;
            color: #ffffff;
        }

        .vc-step-tag-pending {
            background: #cbd5e1;
            color: #475569;
        }

        .dark .vc-step-tag-pending {
            background: #334155;
            color: #94a3b8;
        }

        /* Step PIC Profile Integrated */
        .vc-step-pic-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 8px;
            border-top: 1px dashed var(--vc-card-border);
        }

        .vc-pic-avatar {
            width: 34px;
            height: 34px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .vc-step-pic-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }

        .vc-step-role-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--vc-text-muted);
            line-height: 1.2;
        }

        .vc-step-pic-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--vc-text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }

        /* --- STANDARD CONTENT CARDS --- */
        .vc-card {
            background-color: var(--vc-card-bg);
            border: 1px solid var(--vc-card-border);
            border-radius: var(--vc-radius);
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            box-sizing: border-box;
        }

        .vc-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--vc-card-border);
        }

        .vc-card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--vc-text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* --- RICH CONTENT CONTAINER (FOR BRIEF & CAPTION) --- */
        .vc-rich-content {
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--vc-text-main);
            word-break: break-word;
        }

        .vc-rich-content p {
            margin: 0 0 10px 0;
        }

        .vc-rich-content p:last-child {
            margin-bottom: 0;
        }

        .vc-rich-content strong,
        .vc-rich-content b {
            font-weight: 700;
            color: var(--vc-text-main);
        }

        .vc-rich-content em,
        .vc-rich-content i {
            font-style: italic;
        }

        .vc-rich-content ul {
            list-style-type: disc;
            margin: 8px 0 10px 20px;
            padding: 0;
        }

        .vc-rich-content ol {
            list-style-type: decimal;
            margin: 8px 0 10px 20px;
            padding: 0;
        }

        .vc-rich-content li {
            margin-bottom: 4px;
        }

        .vc-rich-content a {
            color: var(--vc-primary);
            text-decoration: underline;
            font-weight: 500;
        }

        .vc-rich-content blockquote {
            border-left: 3px solid var(--vc-primary);
            margin: 10px 0;
            padding: 6px 12px;
            background: rgba(99, 102, 241, 0.05);
            border-radius: 0 8px 8px 0;
            color: var(--vc-text-muted);
        }

        /* --- CAPTION BOX --- */
        .vc-caption-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            max-height: 420px;
            overflow-y: auto;
            position: relative;
        }

        .dark .vc-caption-box {
            background: #0f172a;
            border-color: #334155;
        }

        .vc-caption-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .vc-caption-stats {
            font-size: 11.5px;
            color: var(--vc-text-muted);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* --- ASSETS & LINKS SECTION --- */
        .vc-asset-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        @media (max-width: 640px) {
            .vc-asset-grid {
                grid-template-columns: 1fr;
            }
        }

        .vc-asset-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .dark .vc-asset-card {
            background: rgba(15, 23, 42, 0.6);
            border-color: #334155;
        }

        .vc-asset-title {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--vc-text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .vc-link-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 12px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #1e293b !important;
            text-decoration: none;
            transition: all 0.15s ease;
            word-break: break-all;
        }

        .dark .vc-link-btn {
            background: #1e293b;
            border-color: #475569;
            color: #f1f5f9 !important;
        }

        .vc-link-btn:hover {
            border-color: var(--vc-primary);
            color: var(--vc-primary) !important;
            background: #f5f3ff;
        }

        .dark .vc-link-btn:hover {
            background: rgba(99, 102, 241, 0.2);
        }

        /* --- REVISION LOG --- */
        .vc-rev-item {
            padding: 12px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 10px;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .dark .vc-rev-item {
            background: rgba(225, 29, 72, 0.1);
            border-color: rgba(225, 29, 72, 0.25);
        }

        .vc-rev-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #e11d48;
        }

        .dark .vc-rev-meta {
            color: #fb7185;
        }

        .vc-rev-note {
            font-size: 12.5px;
            color: #881337;
            line-height: 1.4;
        }

        .dark .vc-rev-note {
            color: #fecdd3;
        }
    </style>

    @php
        $statusLabels = [
            'draft' => 'Draft / Konsep Awal',
            'menunggu_editor' => 'Menunggu Produksi Editor',
            'revisi_editor' => 'Revisi Video / Desain (Editor)',
            'revisi_planner' => 'Revisi Konsep / Bahan (Planner)',
            'siap_publish' => 'Siap Dipublikasikan',
            'selesai' => 'Selesai & Tayang Live',
        ];
        $currentStatusLabel = $statusLabels[$record->status] ?? ucfirst($record->status);

        $creator = $record->instruktur ?? ($record->pegawai ?? $record->planner);
        $creatorRole = $record->instruktur_id ? 'Instruktur' : ($record->pegawai_id ? 'Pengusul' : 'Planner');

        $isFinished = $record->status === 'selesai';
        $canEdit = \App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource::canEdit($record);

        // Tahapan Workflow Stepper (1 s/d 5)
        $stage = match ($record->status) {
            'draft' => 1,
            'menunggu_editor', 'revisi_editor' => 3,
            'revisi_planner' => 2,
            'siap_publish' => 4,
            'selesai' => 5,
            default => 1,
        };
    @endphp

    <div class="vc-container">
        {{-- =========================================================================
           MAIN 2-COLUMN GRID (KIRI: KONTEN LENGKAP | KANAN: CHAT TOP-TO-BOTTOM)
           ========================================================================= --}}
        <div class="vc-layout-grid">

            {{-- =====================================================================
               KOLOM KIRI: DETAIL KONTEN, WORKFLOW + PIC, BRIEF, CAPTION, ASET
               ===================================================================== --}}
            <div class="vc-col-main">

                {{-- 1. HERO BANNER --}}
                <div class="vc-hero-card">
                    <div class="vc-hero-top">
                        <div class="vc-hero-badges">
                            <span class="vc-badge vc-badge-{{ $record->status }}">
                                <span
                                    style="display:inline-block; width:7px; height:7px; border-radius:9999px; background:currentColor;"></span>
                                {{ $currentStatusLabel }}
                            </span>

                            <span
                                class="vc-type-badge {{ $record->jenis_konten === 'final' ? 'vc-type-final' : 'vc-type-bahan' }}">
                                {{ $record->jenis_konten === 'final' ? '✨ Konten Siap (Final)' : '📦 Bahan Mentah' }}
                            </span>

                            <span
                                style="font-size: 11px; font-weight: 700; color: var(--vc-text-muted); background: #f1f5f9; padding: 2px 8px; border-radius: 6px;"
                                class="dark:!bg-slate-800">
                                ID: #{{ $record->id }}
                            </span>
                        </div>

                        <div class="vc-hero-actions">
                            @if (filled($record->link_postingan))
                                <a href="{{ $record->link_postingan }}" target="_blank" rel="noopener noreferrer"
                                    class="vc-btn vc-btn-success">
                                    <span>🚀</span> Buka Link Live
                                </a>
                            @endif

                            @if ($canEdit)
                                <a href="{{ \App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource::getUrl('edit', ['record' => $record->id]) }}"
                                    class="vc-btn vc-btn-primary">
                                    <span>✏️</span> Edit Konten
                                </a>
                            @endif
                        </div>
                    </div>

                    <h1 class="vc-hero-title">
                        {{ $record->nama_kegiatan }}
                    </h1>

                    <div class="vc-hero-meta-row">
                        <div class="vc-hero-meta-item" title="Tanggal Rencana Kegiatan">
                            <span>📅 Target:</span>
                            <strong>{{ \Illuminate\Support\Carbon::parse($record->tanggal_kegiatan)->translatedFormat('l, d F Y') }}</strong>
                        </div>

                        @if ($record->tanggal_posting)
                            <div class="vc-hero-meta-item" title="Tanggal Konten Live di Medsos">
                                <span>🚀 Tayang:</span>
                                <strong>{{ \Illuminate\Support\Carbon::parse($record->tanggal_posting)->translatedFormat('d F Y') }}</strong>
                            </div>
                        @endif

                        <div class="vc-hero-meta-item" title="Waktu dibuat">
                            <span>⏱️ Diajukan:</span>
                            <span>{{ \Illuminate\Support\Carbon::parse($record->created_at)->diffForHumans() }}</span>
                        </div>

                        @if ($record->platforms->isNotEmpty())
                            <div class="vc-hero-platforms">
                                <span>Platform:</span>
                                @foreach ($record->platforms as $platform)
                                    <span class="vc-platform-pill">
                                        @if (stripos($platform->name, 'instagram') !== false)
                                            📷
                                        @elseif (stripos($platform->name, 'facebook') !== false)
                                            📘
                                        @elseif (stripos($platform->name, 'youtube') !== false)
                                            ▶️
                                        @elseif (stripos($platform->name, 'tiktok') !== false)
                                            🎵
                                        @else
                                            🌐
                                        @endif
                                        {{ $platform->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. GABUNGAN: ALUR KERJA & TIM PENANGGUNG JAWAB (PIC) --}}
                <div class="vc-stepper-card">
                    <div class="vc-stepper-heading">
                        <span>⚡ Alur Kerja, PIC & Progres Produksi Konten</span>
                        <span
                            style="font-size: 11px; text-transform: none; font-weight: 600; color: var(--vc-text-muted);">
                            Status saat ini: <strong
                                style="color: var(--vc-primary);">{{ $currentStatusLabel }}</strong>
                        </span>
                    </div>

                    <div class="vc-stepper-grid">
                        {{-- STEP 1: USULAN KONSEP --}}
                        <div
                            class="vc-step-item {{ $stage >= 2 ? 'is-completed' : ($stage === 1 ? 'is-active' : '') }}">
                            <div class="vc-step-top">
                                <span class="vc-step-title">💡 1. Usulan Konsep</span>
                                <span
                                    class="vc-step-state-tag {{ $stage >= 2 ? 'vc-step-tag-done' : ($stage === 1 ? 'vc-step-tag-active' : 'vc-step-tag-pending') }}">
                                    {{ $stage >= 2 ? '✓ Selesai' : ($stage === 1 ? '⚡ Aktif' : '⏳ Menunggu') }}
                                </span>
                            </div>

                            <div class="vc-step-pic-card">
                                <div class="vc-pic-avatar" style="background: #3b82f6;">
                                    {{ strtoupper(mb_substr($creator?->name ?? 'P', 0, 1)) }}
                                </div>
                                <div class="vc-step-pic-info">
                                    <span class="vc-step-role-label">💡 {{ $creatorRole }} (Konsep)</span>
                                    <span class="vc-step-pic-name" title="{{ $creator?->name ?? 'Belum Ditentukan' }}">
                                        {{ $creator?->name ?? 'Belum Ditentukan' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2: PLANNER (BRIEF & JADWAL) --}}
                        <div
                            class="vc-step-item {{ $stage >= 3 ? 'is-completed' : ($stage === 2 ? 'is-active' : '') }}">
                            <div class="vc-step-top">
                                <span class="vc-step-title">📋 2. Brief & Jadwal</span>
                                <span
                                    class="vc-step-state-tag {{ $stage >= 3 ? 'vc-step-tag-done' : ($stage === 2 ? 'vc-step-tag-active' : 'vc-step-tag-pending') }}">
                                    {{ $stage >= 3 ? '✓ Selesai' : ($stage === 2 ? '⚡ Aktif' : '⏳ Menunggu') }}
                                </span>
                            </div>

                            <div class="vc-step-pic-card">
                                <div class="vc-pic-avatar" style="background: #6366f1;">
                                    {{ strtoupper(mb_substr($record->planner?->name ?? 'P', 0, 1)) }}
                                </div>
                                <div class="vc-step-pic-info">
                                    <span class="vc-step-role-label">📋 Medsos Planner</span>
                                    <span class="vc-step-pic-name"
                                        title="{{ $record->planner?->name ?? 'Belum Ditugaskan' }}">
                                        {{ $record->planner?->name ?? 'Belum Ditugaskan' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 3: EDITOR (PRODUKSI DESAIN / VIDEO) --}}
                        <div
                            class="vc-step-item {{ $stage >= 4 ? 'is-completed' : ($stage === 3 ? 'is-active' : '') }}">
                            <div class="vc-step-top">
                                <span class="vc-step-title">🎨 3. Produksi Editor</span>
                                <span
                                    class="vc-step-state-tag {{ $stage >= 4 ? 'vc-step-tag-done' : ($stage === 3 ? 'vc-step-tag-active' : 'vc-step-tag-pending') }}">
                                    {{ $stage >= 4 ? '✓ Selesai' : ($stage === 3 ? '⚡ Editing' : '⏳ Menunggu') }}
                                </span>
                            </div>

                            <div class="vc-step-pic-card">
                                <div class="vc-pic-avatar" style="background: #10b981;">
                                    {{ strtoupper(mb_substr($record->editor?->name ?? 'E', 0, 1)) }}
                                </div>
                                <div class="vc-step-pic-info">
                                    <span class="vc-step-role-label">🎨 Editor Desain/Video</span>
                                    <span class="vc-step-pic-name"
                                        title="{{ $record->editor?->name ?? 'Belum Ditugaskan' }}">
                                        {{ $record->editor?->name ?? 'Belum Ditugaskan' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 4: ADMIN PUBLIKASI (LIVE MEDSOS) --}}
                        <div
                            class="vc-step-item {{ $stage === 5 ? 'is-completed' : ($stage === 4 ? 'is-active' : '') }}">
                            <div class="vc-step-top">
                                <span class="vc-step-title">🚀 4. Tayang (Live)</span>
                                <span
                                    class="vc-step-state-tag {{ $stage === 5 ? 'vc-step-tag-done' : ($stage === 4 ? 'vc-step-tag-active' : 'vc-step-tag-pending') }}">
                                    {{ $stage === 5 ? '✓ Live' : ($stage === 4 ? '⚡ Siap Post' : '⏳ Antrean') }}
                                </span>
                            </div>

                            <div class="vc-step-pic-card">
                                <div class="vc-pic-avatar" style="background: #8b5cf6;">
                                    {{ strtoupper(mb_substr($record->admin?->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="vc-step-pic-info">
                                    <span class="vc-step-role-label">🚀 Admin Publikasi</span>
                                    <span class="vc-step-pic-name" title="{{ $record->admin?->name ?? 'Tim Medsos' }}">
                                        {{ $record->admin?->name ?? 'Tim Medsos' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. CARD: BRIEF & PETUNJUK KONSEP (RICH TEXT) --}}
                <div class="vc-card">
                    <div class="vc-card-header">
                        <span class="vc-card-title">📝 Brief & Deskripsi Konsep</span>
                    </div>

                    @if (filled($record->brief))
                        <div class="vc-rich-content">
                            {!! $record->brief !!}
                        </div>
                    @else
                        <div style="font-size: 13px; color: var(--vc-text-muted); font-style: italic;">
                            Belum ada catatan brief untuk konten ini.
                        </div>
                    @endif

                    @if (filled($record->link_referensi) || filled($record->link_media_mentah))
                        <div
                            style="display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap; padding-top: 10px; border-top: 1px dashed var(--vc-card-border);">
                            @if (filled($record->link_referensi))
                                <a href="{{ $record->link_referensi }}" target="_blank" rel="noopener noreferrer"
                                    class="vc-link-btn" style="flex: 1; min-width: 200px;">
                                    <span>💡 Buka Link Referensi / Ide</span>
                                    <span>↗</span>
                                </a>
                            @endif

                            @if (filled($record->link_media_mentah))
                                <a href="{{ $record->link_media_mentah }}" target="_blank" rel="noopener noreferrer"
                                    class="vc-link-btn" style="flex: 1; min-width: 200px;">
                                    <span>📁 Folder Bahan Mentah (Cloud)</span>
                                    <span>↗</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 4. CARD: CAPTION SOSIAL MEDIA DENGAN FITUR 1-CLICK COPY (RICH TEXT) --}}
                <div class="vc-card">
                    <div class="vc-card-header">
                        <span class="vc-card-title">📋 Teks Caption & Hashtag</span>
                        @if (filled($record->caption))
                            <button type="button" id="copyCaptionBtn"
                                onclick="copyCaptionToClipboard('captionFormattedText', this)"
                                class="vc-btn vc-btn-secondary" style="padding: 5px 12px; font-size: 12px;">
                                <span>📋</span> Salin Caption
                            </button>
                        @endif
                    </div>

                    @if (filled($record->caption))
                        <div class="vc-caption-box vc-rich-content" id="captionFormattedText">{!! $record->caption !!}
                        </div>
                        <div class="vc-caption-footer">
                            <div class="vc-caption-stats">
                                <span>📊 <strong>{{ str_word_count(strip_tags($record->caption)) }}</strong>
                                    kata</span>
                                <span>•</span>
                                <span>🔤 <strong>{{ mb_strlen(strip_tags($record->caption)) }}</strong> karakter</span>
                            </div>
                            <span style="font-size: 11px; color: var(--vc-text-muted);">Siap di-copy & paste ke
                                platform sosmed</span>
                        </div>
                    @else
                        <div style="font-size: 13px; color: var(--vc-text-muted); font-style: italic;">
                            Caption belum dibuat oleh tim planner/editor.
                        </div>
                    @endif
                </div>

                {{-- 5. CARD: ASET MEDIA & HASIL PRODUKSI --}}
                <div class="vc-card">
                    <div class="vc-card-header">
                        <span class="vc-card-title">🎬 Aset Media & Tautan Hasil Produksi</span>
                    </div>

                    <div class="vc-asset-grid">
                        {{-- SUB-CARD: BAHAN MENTAH --}}
                        <div class="vc-asset-card">
                            <div class="vc-asset-title">
                                <span>📦 Bahan Mentah (Planner)</span>
                            </div>

                            @if (filled($record->link_media_mentah))
                                <a href="{{ $record->link_media_mentah }}" target="_blank" rel="noopener noreferrer"
                                    class="vc-link-btn">
                                    <span>📁 Buka Link Bahan Mentah</span>
                                    <span>↗</span>
                                </a>
                            @else
                                <span style="font-size: 12px; color: var(--vc-text-muted);">Tidak ada tautan drive
                                    bahan mentah.</span>
                            @endif

                            @if ($record->hasMedia('mentah'))
                                <button type="button" wire:click="downloadZip('mentah')"
                                    class="vc-btn vc-btn-secondary"
                                    style="font-size: 12px; width: 100%; justify-content: center;">
                                    <span>📦</span> Unduh File Mentah (.zip)
                                </button>
                            @endif
                        </div>

                        {{-- SUB-CARD: HASIL EDITING FINAL --}}
                        <div class="vc-asset-card">
                            <div class="vc-asset-title">
                                <span>✨ Hasil Final (Editor)</span>
                            </div>

                            @if (filled($record->link_hasil_edit))
                                <a href="{{ $record->link_hasil_edit }}" target="_blank" rel="noopener noreferrer"
                                    class="vc-link-btn" style="border-color: #a7f3d0; background: #f0fdf4;">
                                    <span>🎬 Buka Video / Desain Final</span>
                                    <span>↗</span>
                                </a>
                            @else
                                <span style="font-size: 12px; color: var(--vc-text-muted);">Belum ada link hasil edit
                                    final.</span>
                            @endif

                            @if ($record->hasMedia('hasil_edit'))
                                <button type="button" wire:click="downloadZip('hasil_edit')"
                                    class="vc-btn vc-btn-success"
                                    style="font-size: 12px; width: 100%; justify-content: center;">
                                    <span>✨</span> Unduh Hasil Final (.zip)
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 6. CARD: STATUS PUBLIKASI MEDSOS (JIKA SUDAH TAYANG) --}}
                @if ($record->link_postingan)
                    <div class="vc-card"
                        style="border-color: #a7f3d0; background: linear-gradient(135deg, rgba(240, 253, 244, 0.9), rgba(255, 255, 255, 0.95));">
                        <div class="vc-card-header" style="border-color: #bbf7d0;">
                            <span class="vc-card-title" style="color: #15803d;">🌐 Status Publikasi Medsos</span>
                            <span
                                style="font-size: 11px; font-weight: 700; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 9999px;">
                                ✓ Sudah Tayang
                            </span>
                        </div>

                        <div
                            style="display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;">
                            <div style="font-size: 13px; color: #166534;">
                                Konten telah dipublikasikan pada
                                <strong>{{ $record->tanggal_posting ? \Illuminate\Support\Carbon::parse($record->tanggal_posting)->translatedFormat('d F Y') : '-' }}</strong>.
                            </div>
                            <a href="{{ $record->link_postingan }}" target="_blank" rel="noopener noreferrer"
                                class="vc-btn vc-btn-success">
                                <span>🚀</span> Kunjungi Postingan Live ↗
                            </a>
                        </div>
                    </div>
                @endif

                {{-- 7. CARD: RIWAYAT REVISI (JIKA PERNAH ADA) --}}
                @if ($record->revisions->isNotEmpty())
                    <div class="vc-card">
                        <div class="vc-card-header">
                            <span class="vc-card-title">🔄 Riwayat Permintaan Revisi</span>
                            <span
                                style="font-size: 11px; font-weight: 700; color: #e11d48; background: #ffe4e6; padding: 2px 8px; border-radius: 9999px;">
                                {{ $record->revisions->count() }} Kali Revisi
                            </span>
                        </div>

                        @foreach ($record->revisions as $rev)
                            <div class="vc-rev-item">
                                <div class="vc-rev-meta">
                                    <span>
                                        Target: <strong>{{ ucfirst($rev->target_revisi) }}</strong>
                                        @if ($rev->user)
                                            • Oleh: {{ $rev->user->name }}
                                        @endif
                                    </span>
                                    <span>{{ \Illuminate\Support\Carbon::parse($rev->created_at)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="vc-rev-note">
                                    {{ $rev->catatan }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

            {{-- =====================================================================
               KOLOM KANAN: DISKUSI & OBROLAN TIM DARI ATAS SAMPAI BAWAH
               ===================================================================== --}}
            <div class="vc-col-sidebar">
                <div class="vc-sidebar-chat-card">
                    <div class="vc-chat-header">
                        <div class="vc-chat-title-group">
                            <span class="vc-chat-main-title">💬 Diskusi & Obrolan Tim</span>
                            <span class="vc-chat-sub">Ruang obrolan internal & koordinasi konten</span>
                        </div>
                    </div>

                    <div class="vc-chat-body">
                        @livewire('content-comments', ['contentId' => $record->id], key('view-content-comments-' . $record->id))
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT INTERAKTIF SALIN CAPTION (TEKS BERSIH TANPA TAG HTML) --}}
    <script>
        function copyCaptionToClipboard(containerId, btnElement) {
            var el = document.getElementById(containerId);
            if (!el) return;

            // Mengambil innerText rendered di browser (otomatis membuang tag HTML dan mempertahankan baris baru)
            var text = (el.innerText || el.textContent || '').trim();
            if (!text) return;

            function onSuccess() {
                var originalHtml = btnElement.innerHTML;
                btnElement.innerHTML = '<span>✅</span> Tersalin!';
                btnElement.style.backgroundColor = '#10b981';
                btnElement.style.color = '#ffffff';
                setTimeout(function() {
                    btnElement.innerHTML = originalHtml;
                    btnElement.style.backgroundColor = '';
                    btnElement.style.color = '';
                }, 2500);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(onSuccess).catch(fallback);
            } else {
                fallback();
            }

            function fallback() {
                var textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    onSuccess();
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }
                document.body.removeChild(textArea);
            }
        }
    </script>
</x-filament-panels::page>
