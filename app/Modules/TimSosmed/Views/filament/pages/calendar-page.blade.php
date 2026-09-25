<x-filament-panels::page wire:poll.visible.15s="checkCalendarUpdates">
    {{-- ================= FULLCALENDAR & STYLING ================= --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

    <style>
        /* ========================================================
           MODERN FULLCALENDAR THEMING
           ======================================================== */
        .fc {
            font-family: inherit;
            --fc-border-color: #e2e8f0;
            --fc-today-bg-color: rgba(99, 102, 241, 0.04);
            --fc-page-bg-color: transparent;
        }

        .dark .fc {
            --fc-border-color: #334155;
            --fc-today-bg-color: rgba(99, 102, 241, 0.08);
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 12px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.35rem !important;
            font-weight: 800 !important;
            color: #1e293b;
            letter-spacing: -0.02em;
        }

        .dark .fc .fc-toolbar-title {
            color: #f1f5f9;
        }

        .fc .fc-button {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            padding: 7px 14px !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            transition: all 0.15s ease !important;
            text-transform: capitalize !important;
        }

        .fc .fc-button:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
        }

        .dark .fc .fc-button {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #e2e8f0 !important;
        }

        .dark .fc .fc-button:hover {
            background-color: #334155 !important;
            color: #ffffff !important;
        }

        .fc .fc-col-header-cell {
            padding: 8px 4px !important;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        .dark .fc .fc-col-header-cell {
            background-color: #0f172a;
            border-bottom-color: #334155 !important;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b !important;
        }

        .dark .fc .fc-col-header-cell-cushion {
            color: #94a3b8 !important;
        }

        .fc .fc-daygrid-day-number {
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            color: #64748b;
            padding: 6px 8px !important;
        }

        .dark .fc .fc-daygrid-day-number {
            color: #94a3b8;
        }

        .fc-day-today .fc-daygrid-day-number {
            background-color: #4f46e5;
            color: #ffffff !important;
            border-radius: 9999px;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 4px;
            padding: 0 !important;
        }

        .fc .fc-daygrid-day-frame {
            min-height: 140px !important;
            height: 100% !important;
            cursor: pointer;
            transition: background-color 0.15s ease;
            display: flex !important;
            flex-direction: column !important;
            box-sizing: border-box !important;
        }

        .fc .fc-daygrid-day-top {
            flex-shrink: 0;
            display: flex;
            flex-direction: row-reverse;
            padding: 2px 4px 0 4px;
        }

        .fc .fc-daygrid-day:hover .fc-daygrid-day-frame {
            background-color: rgba(99, 102, 241, 0.04);
        }

        .dark .fc .fc-daygrid-day:hover .fc-daygrid-day-frame {
            background-color: rgba(99, 102, 241, 0.08);
        }

        .fc-daygrid-day-events {
            margin: 0 !important;
            padding: 2px 4px 6px 4px !important;
            flex: 1 1 auto;
            display: flex !important;
            flex-direction: column !important;
            gap: 5px !important;
            position: relative !important;
            min-height: 0;
        }

        /* OVERRIDE ABSOLUTE POSITIONING TO PREVENT CARDS FROM OVERLAPPING */
        .fc .fc-daygrid-event-harness,
        .fc .fc-daygrid-event-harness-abs {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            margin: 0 !important;
            margin-bottom: 5px !important;
            display: block !important;
            visibility: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            z-index: 1 !important;
        }

        .fc-event,
        .fc-event-main,
        .fc-daygrid-event {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            cursor: pointer !important;
            white-space: normal !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* ========================================================
           CONTROL BAR & HEADER STYLES (100% SELF-CONTAINED CSS)
           ======================================================== */
        .cal-control-bar {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 18px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            box-sizing: border-box;
        }

        .dark .cal-control-bar {
            background-color: #0f172a;
            border-color: #334155;
        }

        .cal-legend-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cal-legend-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-right: 4px;
        }

        .dark .cal-legend-label {
            color: #94a3b8;
        }

        .cal-status-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .cal-actions-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .cal-hint-text {
            font-size: 12px;
            color: #64748b;
            display: inline-block;
        }

        .dark .cal-hint-text {
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .cal-hint-text {
                display: none;
            }
        }

        .cal-btn-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3);
            transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1.4;
            white-space: nowrap;
        }

        .cal-btn-add:hover {
            background-color: #4338ca;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.4);
            transform: translateY(-1px);
        }

        .cal-btn-secondary {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1.4;
        }

        .dark .cal-btn-secondary {
            background-color: #1e293b;
            color: #e2e8f0;
            border-color: #475569;
        }

        .cal-btn-secondary:hover {
            background-color: #f1f5f9;
            border-color: #94a3b8;
        }

        /* ========================================================
           STATUS BADGE PILLS
           ======================================================== */
        .cal-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 9999px;
            line-height: 1.2;
            letter-spacing: 0.02em;
        }

        .status-draft {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .status-menunggu_editor {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-revisi_editor,
        .status-revisi_planner {
            background-color: #ffe4e6;
            color: #e11d48;
            border: 1px solid #fecdd3;
        }

        .status-siap_publish {
            background-color: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        .status-selesai {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .dark .status-draft {
            background-color: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border-color: #475569;
        }

        .dark .status-menunggu_editor {
            background-color: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .dark .status-revisi_editor,
        .dark .status-revisi_planner {
            background-color: rgba(225, 29, 72, 0.15);
            color: #fb7185;
            border-color: rgba(225, 29, 72, 0.3);
        }

        .dark .status-siap_publish {
            background-color: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border-color: rgba(14, 165, 233, 0.3);
        }

        .dark .status-selesai {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }

        /* ========================================================
           KARTU EVENT MODERN FULLCALENDAR (RESPONSIF & BEBAS OVERLAP)
           ======================================================== */
        .cal-event-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 6px 7px 6px 9px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 3px;
            text-align: left;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .dark .cal-event-card {
            background-color: #1e293b;
            border-color: #334155;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2);
        }

        .cal-event-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px -2px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .dark .cal-event-card:hover {
            border-color: #64748b;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.4);
        }

        .cal-status-stripe {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3.5px;
            border-radius: 9px 0 0 9px;
        }

        .cal-top-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 2px;
        }

        .cal-type-tag {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1px 4.5px;
            border-radius: 4px;
            background-color: #f1f5f9;
            color: #475569;
            white-space: nowrap;
            line-height: 1.2;
            flex-shrink: 0;
        }

        .dark .cal-type-tag {
            background-color: #334155;
            color: #cbd5e1;
        }

        /* Badge Obrolan / Diskusi Tim di Kartu Kalender */
        .cal-chat-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 9999px;
            line-height: 1.2;
            transition: all 0.15s ease;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cal-chat-badge.is-read {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .dark .cal-chat-badge.is-read {
            background-color: #334155;
            color: #cbd5e1;
            border-color: #475569;
        }

        .cal-chat-badge.is-unread {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.2);
            animation: cal-pulse-unread 2s infinite ease-in-out;
        }

        .dark .cal-chat-badge.is-unread {
            background-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-color: #ef4444;
        }

        @keyframes cal-pulse-unread {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .cal-unread-dot {
            width: 5px;
            height: 5px;
            border-radius: 9999px;
            background-color: #ef4444;
            display: inline-block;
            box-shadow: 0 0 4px rgba(239, 68, 68, 0.6);
            flex-shrink: 0;
        }

        .cal-unread-pill {
            background-color: #ef4444;
            color: #ffffff;
            font-size: 8.5px;
            padding: 0 3.5px;
            border-radius: 9999px;
            font-weight: 800;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }

        .cal-card-title {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            margin: 2px 0 3px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
            width: 100%;
        }

        .dark .cal-card-title {
            color: #f1f5f9;
        }

        .cal-processor-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 3px 6px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            box-sizing: border-box;
            width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .dark .cal-processor-badge {
            background-color: rgba(30, 41, 59, 0.6);
            border-color: #334155;
        }

        .cal-processor-icon {
            font-size: 13px;
            line-height: 1;
            flex-shrink: 0;
        }

        .cal-processor-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .cal-processor-role {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .cal-processor-role {
            color: #94a3b8;
        }

        .cal-processor-name {
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .cal-processor-name {
            color: #f8fafc;
        }

        .cal-card-subteam {
            font-size: 9px;
            font-weight: 600;
            color: #64748b;
            padding: 0 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            width: 100%;
        }

        .dark .cal-card-subteam {
            color: #94a3b8;
        }

        .cal-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            flex-wrap: wrap;
            padding-top: 4px;
            margin-top: 2px;
            border-top: 1px dashed #f1f5f9;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .dark .cal-card-footer {
            border-top-color: #334155;
        }

        .cal-platforms-row {
            display: flex;
            align-items: center;
            gap: 2.5px;
            flex-wrap: wrap;
            min-width: 0;
        }

        .cal-bottom-chat {
            display: flex;
            align-items: center;
            margin-left: auto;
            flex-shrink: 0;
        }

        .cal-platform-chip {
            font-size: 8.5px;
            font-weight: 600;
            padding: 0.5px 4px;
            border-radius: 3.5px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            white-space: nowrap;
            line-height: 1.2;
            flex-shrink: 0;
        }

        .dark .cal-platform-chip {
            background-color: #0f172a;
            border-color: #334155;
            color: #94a3b8;
        }

        .cal-team-avatars {
            font-size: 9.5px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 2px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .dark .cal-team-avatars {
            color: #94a3b8;
        }

        /* ========================================================
           POPUP MODAL IFRAME (HALAMAN FORM ASLI)
           ======================================================== */
        .cal-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background-color: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            animation: fadeIn 0.15s ease-out;
            box-sizing: border-box;
        }

        .cal-modal-dialog {
            background-color: #ffffff;
            border-radius: 18px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 95vw;
            max-width: 1350px;
            height: 92vh;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-sizing: border-box;
        }

        .dark .cal-modal-dialog {
            background-color: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }

        .cal-modal-header {
            padding: 12px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background-color: #f8fafc;
            flex-shrink: 0;
        }

        .dark .cal-modal-header {
            background-color: #1e293b;
            border-bottom-color: #334155;
        }

        .cal-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid rgba(99, 102, 241, 0.2);
            border-top-color: #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modalPop {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* ========================================================
           PENANDA DEADLINE KARTU EVENT (PALING BAWAH KARTU)
           ======================================================== */
        .cal-event-card.has-deadline-overdue {
            border-color: #fca5a5 !important;
            box-shadow: 0 1px 3px rgba(239, 68, 68, 0.15);
        }

        .dark .cal-event-card.has-deadline-overdue {
            border-color: rgba(239, 68, 68, 0.5) !important;
            box-shadow: 0 1px 4px rgba(239, 68, 68, 0.25);
        }

        .cal-event-card.has-deadline-today {
            border-color: #fcd34d !important;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.15);
        }

        .dark .cal-event-card.has-deadline-today {
            border-color: rgba(245, 158, 11, 0.5) !important;
            box-shadow: 0 1px 4px rgba(245, 158, 11, 0.25);
        }

        .cal-card-deadline-tag {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 3px 6px;
            border-radius: 6px;
            font-size: 8.5px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: 0.02em;
            width: 100%;
            box-sizing: border-box;
            margin-top: 4px;
            text-align: center;
        }

        .cal-card-deadline-tag.cal-card-deadline-overdue {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .dark .cal-card-deadline-tag.cal-card-deadline-overdue {
            background-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.4);
        }

        .cal-card-deadline-tag.cal-card-deadline-today {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .dark .cal-card-deadline-tag.cal-card-deadline-today {
            background-color: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
            border-color: rgba(245, 158, 11, 0.4);
        }

        .cal-deadline-pulse-dot {
            width: 5px;
            height: 5px;
            border-radius: 9999px;
            background-color: currentColor;
            flex-shrink: 0;
            animation: cal-pulse-anim 1.8s infinite;
        }

        @keyframes cal-pulse-anim {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .cal-deadline-tag-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 800;
        }
    </style>

    {{-- ================= CONTROL BAR & LEGENDA ATAS ================= --}}
    <div class="cal-control-bar">
        {{-- Status Indicators --}}
        <div class="cal-legend-group">
            <span class="cal-legend-label">Status:</span>
            <span class="cal-badge-pill status-draft">
                <span class="cal-status-dot" style="background-color: #64748b;"></span> Draft
            </span>
            <span class="cal-badge-pill status-menunggu_editor">
                <span class="cal-status-dot" style="background-color: #f59e0b;"></span> Menunggu Editor
            </span>
            <span class="cal-badge-pill status-revisi_editor">
                <span class="cal-status-dot" style="background-color: #ef4444;"></span> Revisi
            </span>
            <span class="cal-badge-pill status-siap_publish">
                <span class="cal-status-dot" style="background-color: #3b82f6;"></span> Siap Publish
            </span>
            <span class="cal-badge-pill status-selesai">
                <span class="cal-status-dot" style="background-color: #10b981;"></span> Selesai / Live
            </span>
        </div>

        {{-- Actions & Hint --}}
        <div class="cal-actions-group">
            <span class="cal-hint-text">
                💡 <strong>Klik jadwal</strong> untuk buka edit &bull; <strong>Klik tanggal</strong> buat konten
            </span>
            <button type="button"
                onclick="openContentPopup('{{ route('filament.admin.resources.contents.create') }}', '✨ Buat Rencana Konten Baru', 'Buat')"
                class="cal-btn-add">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Konten</span>
            </button>
        </div>
    </div>

    {{-- ================= CONTAINER KALENDER ================= --}}
    <div style="background-color: #ffffff; padding: 18px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
        class="dark:!bg-gray-900 dark:!border-gray-800">
        <div id='calendar' wire:ignore></div>
    </div>



    {{-- ================= POPUP MODAL: HALAMAN FORM ASLI (EDIT / CREATE) ================= --}}
    <div id="contentFrameModal" class="cal-modal-backdrop" style="display: none;"
        onclick="if(event.target === this) closeContentPopup();">
        <div class="cal-modal-dialog">
            {{-- Header Modal --}}
            <div class="cal-modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span id="frameModalBadge" class="cal-type-tag">Form Konten</span>
                    <h3 id="frameModalTitle" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;"
                        class="dark:!text-gray-100">
                        Edit Konten
                    </h3>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <a id="frameOpenNewTab" href="#" target="_blank" class="cal-btn-secondary"
                        style="font-size: 12px; padding: 6px 12px; text-decoration: none;">
                        ↗ Buka di Tab Baru
                    </a>
                    <button type="button" onclick="closeContentPopup()" class="cal-btn-secondary"
                        style="font-size: 12px; padding: 6px 14px; background-color: #fee2e2; color: #b91c1c; border-color: #fca5a5;">
                        ✕ Tutup (Kembali ke Kalender)
                    </button>
                </div>
            </div>

            {{-- Body Modal: IFRAME HALAMAN RESMI FILAMENT --}}
            <div style="flex: 1; width: 100%; height: calc(100% - 53px); position: relative; background-color: #f8fafc;"
                class="dark:!bg-gray-900">
                {{-- Spinner Loading Indikator --}}
                <div id="frameLoading"
                    style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.85); z-index: 10;"
                    class="dark:!bg-gray-900/80">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                        <div class="cal-spinner"></div>
                        <span style="font-size: 13px; font-weight: 600; color: #475569;" class="dark:!text-gray-300">
                            Memuat form konten...
                        </span>
                    </div>
                </div>

                <iframe id="contentIframe" src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>

    {{-- ================= JAVASCRIPT INITIALIZATION ================= --}}
    <script>
        var calendar = null;

        function closeContentPopup() {
            var modal = document.getElementById('contentFrameModal');
            var iframe = document.getElementById('contentIframe');
            if (modal) modal.style.display = 'none';
            if (iframe) iframe.src = 'about:blank';

            // Refresh FullCalendar events dari server
            if (calendar) {
                @this.getCalendarEvents().then(events => {
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                });
            }
        }
        window.closeContentPopup = closeContentPopup;

        // Listener pesan dari iframe anak saat disimpan atau dibatalkan
        window.addEventListener('message', function(event) {
            if (event.data === 'content-saved' || event.data?.type === 'content-saved' ||
                event.data === 'close-content-popup' || event.data?.type === 'close-content-popup') {
                closeContentPopup();
            }
        });

        function openContentPopup(url, title, badge) {
            var modal = document.getElementById('contentFrameModal');
            var iframe = document.getElementById('contentIframe');
            var titleEl = document.getElementById('frameModalTitle');
            var badgeEl = document.getElementById('frameModalBadge');
            var newTabEl = document.getElementById('frameOpenNewTab');
            var loadingEl = document.getElementById('frameLoading');

            titleEl.textContent = title || 'Form Konten';
            badgeEl.textContent = badge || 'Konten';
            newTabEl.href = url;
            loadingEl.style.display = 'flex';

            iframe.src = url;
            modal.style.display = 'flex';

            iframe.onload = function() {
                loadingEl.style.display = 'none';
                try {
                    var iframeWin = iframe.contentWindow;
                    var iframeDoc = iframe.contentDocument || iframeWin.document;
                    var currentPath = iframeWin.location.pathname;

                    // Jika iframe dialihkan ke calendar-page atau admin/contents setelah create/edit/action disimpan
                    if (currentPath.includes('calendar-page') || currentPath === '/admin/contents' || currentPath ===
                        '/admin/contents/') {
                        closeContentPopup();
                        return;
                    }

                    if (iframeDoc && iframeDoc.head) {
                        var style = iframeDoc.createElement('style');
                        style.innerHTML = `
                            aside.fi-sidebar,
                            nav.fi-topbar,
                            header.fi-topbar,
                            .fi-sidebar { display: none !important; }
                            .fi-main { padding-top: 10px !important; margin-left: 0 !important; max-width: 100% !important; width: 100% !important; }
                            .fi-page { padding: 12px 18px !important; }
                            .fi-header { margin-bottom: 12px !important; }
                        `;
                        iframeDoc.head.appendChild(style);
                    }
                } catch (e) {
                    console.log('Iframe styling notice:', e);
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                contentHeight: 'auto',
                handleWindowResize: true,
                fixedWeekCount: false,
                dayMaxEvents: false,
                expandRows: true,

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },

                buttonText: {
                    today: 'Hari Ini'
                },

                events: {!! $events !!},

                // 1. KLIK TANGGAL KOSONG (BUAT KONTEN BARU DENGAN TANGGAL TERSEBUT DI POPUP)
                dateClick: function(info) {
                    var createUrl =
                        "{{ route('filament.admin.resources.contents.create') }}?tanggal_kegiatan=" +
                        info.dateStr;
                    openContentPopup(createUrl, '✨ Buat Rencana Konten: ' + info.dateStr, 'Buat Baru');
                },

                // 2. KLIK EVENT (BUKA POPUP: VIEW JIKA SUDAH SELESAI / EDIT JIKA MASIH PROSES)
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    var contentId = parseInt(info.event.id);
                    if (contentId) {
                        @this.markAsRead(contentId);
                        var props = info.event.extendedProps || {};
                        var isFinished = props.status === 'selesai' || props.is_selesai || props
                            .action_type === 'view' || !props.can_edit;
                        var targetUrl = props.action_url || (isFinished ? ("/admin/contents/" +
                            contentId) : ("/admin/contents/" + contentId + "/edit"));
                        var modalTitle = (isFinished ? '👁️ Detail Konten: ' : '✏️ Edit: ') + info.event
                            .title;
                        var modalBadge = (isFinished ? 'Lihat Konten #' : 'Edit Konten #') + contentId;
                        openContentPopup(targetUrl, modalTitle, modalBadge);
                    }
                },

                // 3. DESAIN KARTU EVENT MODERN (JUDUL LENGKAP & PETUGAS JELAS)
                eventContent: function(arg) {
                    var props = arg.event.extendedProps || {};
                    var status = props.status || 'draft';
                    var statusLabel = props.status_label || 'Draft';
                    var statusColor = props.status_color || arg.event.backgroundColor || '#64748b';
                    var jenisKonten = props.jenis_konten || 'Bahan';
                    var platforms = props.platforms || [];
                    var petugasRole = props.petugas_role || 'Petugas';
                    var petugasName = props.petugas_name || '-';
                    var petugasIcon = props.petugas_icon || '👤';
                    var konseptor = props.konseptor || null;
                    var commentsCount = props.comments_count || 0;
                    var unreadCount = props.unread_comments_count || 0;
                    var hasUnread = props.has_unread_comments || false;
                    var isFinished = status === 'selesai' || props.is_selesai || props.action_type ===
                        'view' || !props.can_edit;
                    var actionHint = isFinished ? 'melihat detail' : 'membuka & mengedit';

                    // Penanda Deadline di Bagian Paling Bawah Kartu
                    var isDeadline = props.is_deadline || false;
                    var isOverdue = props.is_overdue || false;
                    var isToday = props.is_today || false;
                    var deadlineLabel = props.deadline_label || '';

                    var deadlineMarkerHtml = '';
                    if (isDeadline && deadlineLabel) {
                        var deadlineClass = isOverdue ? 'cal-card-deadline-overdue' :
                            'cal-card-deadline-today';
                        deadlineMarkerHtml = `
                            <div class="cal-card-deadline-tag ${deadlineClass}" title="Tenggat Waktu: ${deadlineLabel}">
                                <span class="cal-deadline-pulse-dot"></span>
                                <span class="cal-deadline-tag-text">${deadlineLabel}</span>
                            </div>
                        `;
                    }

                    var cardDeadlineBorderClass = '';
                    if (isDeadline) {
                        cardDeadlineBorderClass = isOverdue ? 'has-deadline-overdue' :
                            'has-deadline-today';
                    }

                    var chatBadgeHtml = '';
                    if (hasUnread) {
                        chatBadgeHtml = `
                            <span class="cal-chat-badge is-unread" title="${commentsCount} diskusi (${unreadCount} pesan baru belum dibaca)">
                                <span class="cal-unread-dot"></span>
                                💬 ${commentsCount}
                                <span class="cal-unread-pill">${unreadCount} baru</span>
                            </span>
                        `;
                    } else if (commentsCount > 0) {
                        chatBadgeHtml = `
                            <span class="cal-chat-badge is-read" title="${commentsCount} diskusi obrolan tim">
                                💬 ${commentsCount}
                            </span>
                        `;
                    }

                    var platformsHtml = '';
                    if (platforms && platforms.length > 0) {
                        if (platforms.length <= 2) {
                            platformsHtml = platforms.map(p =>
                                `<span class="cal-platform-chip">${p}</span>`).join('');
                        } else {
                            platformsHtml = platforms.slice(0, 2).map(p =>
                                    `<span class="cal-platform-chip">${p}</span>`).join('') +
                                `<span class="cal-platform-chip" title="${platforms.slice(2).join(', ')}">+${platforms.length - 2}</span>`;
                        }
                    }

                    var subteamHtml = '';
                    if (konseptor && status !== 'draft') {
                        subteamHtml =
                            `<div class="cal-card-subteam" title="Pembuat Konsep: ${konseptor}">💡 Konsep: <strong>${konseptor}</strong></div>`;
                    }

                    var cardHtml = `
                        <div class="cal-event-card ${cardDeadlineBorderClass}" title="Klik untuk ${actionHint} konten: ${arg.event.title}">
                            <div class="cal-status-stripe" style="background-color: ${statusColor};"></div>

                            <div class="cal-top-meta">
                                <span class="cal-badge-pill status-${status}">
                                    <span style="display:inline-block; width:5px; height:5px; border-radius:9999px; background-color:${statusColor}; flex-shrink:0;"></span>
                                    ${statusLabel}
                                </span>
                                <span class="cal-type-tag">${jenisKonten}</span>
                            </div>

                            <div class="cal-card-title">${arg.event.title}</div>

                            <div class="cal-processor-badge" title="Petugas: ${petugasRole} (${petugasName})">
                                <span class="cal-processor-icon">${petugasIcon}</span>
                                <div class="cal-processor-details">
                                    <span class="cal-processor-role">${petugasRole}</span>
                                    <span class="cal-processor-name">${petugasName}</span>
                                </div>
                            </div>

                            ${subteamHtml}

                            ${(platformsHtml || chatBadgeHtml) ? `
                                    <div class="cal-card-footer">
                                        ${platformsHtml ? `<div class="cal-platforms-row">${platformsHtml}</div>` : '<div></div>'}
                                        ${chatBadgeHtml ? `<div class="cal-bottom-chat">${chatBadgeHtml}</div>` : ''}
                                    </div>
                                ` : ''}

                            ${deadlineMarkerHtml}
                        </div>
                    `;

                    return {
                        html: cardHtml
                    };
                }
            });

            calendar.render();

            setTimeout(function() {
                calendar.updateSize();
            }, 150);

            window.addEventListener('resize', function() {
                calendar.updateSize();
            });

            // Re-render saat ada event refresh dari Livewire
            Livewire.on('calendar-refresh', () => {
                @this.getCalendarEvents().then(events => {
                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                });
            });
        });
    </script>
</x-filament-panels::page>
