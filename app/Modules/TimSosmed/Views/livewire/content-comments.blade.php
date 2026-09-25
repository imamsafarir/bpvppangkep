<div class="ts-comments-wrapper" x-data="{
    scrollToBottom() {
        const list = $el.querySelector('.ts-comments-list');
        if (list) {
            list.scrollTop = list.scrollHeight;
        }
    }
}" x-init="scrollToBottom()"
    @comment-added.window="setTimeout(() => scrollToBottom(), 60)">
    <style>
        .ts-comments-wrapper {
            font-family: inherit;
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .ts-comments-wrapper *,
        .ts-comments-wrapper *::before,
        .ts-comments-wrapper *::after {
            box-sizing: border-box;
        }

        .ts-comments-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 400px;
            overflow-y: auto;
            padding: 4px 6px 4px 2px;
            scroll-behavior: smooth;
        }

        .ts-comments-list::-webkit-scrollbar {
            width: 5px;
        }

        .ts-comments-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .ts-comments-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        .ts-comments-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .ts-comment-card {
            border-radius: 12px;
            padding: 10px 12px;
            border-width: 1px;
            border-style: solid;
            transition: all 0.2s ease;
            position: relative;
        }

        /* Pesan dari user yang sedang login */
        .ts-comment-card.is-me {
            background-color: #f0f4ff;
            border-color: #c7d2fe;
            margin-left: 12px;
            border-bottom-right-radius: 3px;
        }

        /* Pesan dari anggota tim lainnya */
        .ts-comment-card.is-other {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            margin-right: 12px;
            border-bottom-left-radius: 3px;
        }

        .ts-comment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 6px;
        }

        .ts-author-info {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ts-avatar {
            width: 22px;
            height: 22px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 700;
            color: #ffffff;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .ts-author-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .ts-role-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 1.5px 7px;
            border-radius: 9999px;
            border-width: 1px;
            border-style: solid;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            line-height: 1.2;
        }

        .ts-role-admin {
            background-color: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .ts-role-planner {
            background-color: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .ts-role-editor {
            background-color: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .ts-role-admin-platform {
            background-color: #eff6ff;
            color: #1e40af;
            border-color: #bfdbfe;
        }

        .ts-role-staff {
            background-color: #f5f3ff;
            color: #5b21b6;
            border-color: #ddd6fe;
        }

        .ts-role-user {
            background-color: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        .ts-time {
            font-size: 10.5px;
            color: #64748b;
        }

        .ts-delete-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 3px;
            border-radius: 4px;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .ts-delete-btn:hover {
            color: #ef4444;
            background-color: #fee2e2;
        }

        .ts-comment-body {
            font-size: 12.5px;
            color: #334155;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .ts-empty-box {
            text-align: center;
            padding: 22px 14px;
            background-color: #f8fafc;
            border-radius: 12px;
            border: 1.5px dashed #cbd5e1;
        }

        .ts-empty-icon {
            font-size: 26px;
            margin-bottom: 4px;
        }

        .ts-empty-title {
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
        }

        .ts-empty-sub {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 3px;
            line-height: 1.35;
        }

        .ts-input-section {
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .ts-input-row {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .ts-textarea {
            width: 100%;
            min-height: 60px;
            max-height: 140px;
            padding: 8px 12px;
            font-size: 12.5px;
            font-family: inherit;
            line-height: 1.4;
            border-radius: 10px;
            border: 1.5px solid #cbd5e1;
            background-color: #ffffff;
            color: #1e293b;
            resize: vertical;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .ts-textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .ts-textarea::placeholder {
            color: #94a3b8;
        }

        .ts-send-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #4f46e5;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease;
            white-space: nowrap;
            height: 42px;
            flex-shrink: 0;
        }

        .ts-send-button:hover:not(:disabled) {
            background: #4338ca;
            box-shadow: 0 3px 6px -1px rgba(79, 70, 229, 0.25);
        }

        .ts-send-button:active:not(:disabled) {
            transform: scale(0.98);
        }

        .ts-send-button:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .ts-btn-loading-icon {
            display: none;
            align-items: center;
            justify-content: center;
        }

        .ts-spinner-svg {
            width: 15px;
            height: 15px;
            animation: ts-spin 0.85s linear infinite;
        }

        @keyframes ts-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .ts-helper-text {
            font-size: 10.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ts-alert-draft {
            padding: 12px 14px;
            border-radius: 10px;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 12px;
            line-height: 1.4;
        }

        .ts-error-text {
            font-size: 11px;
            color: #ef4444;
            margin-top: 2px;
        }

        /* Penyesuaian Dark Mode Filament */
        .dark .ts-comment-card.is-me {
            background-color: rgba(99, 102, 241, 0.14);
            border-color: rgba(99, 102, 241, 0.35);
        }

        .dark .ts-comment-card.is-other {
            background-color: #1e293b;
            border-color: #334155;
        }

        .dark .ts-author-name {
            color: #f1f5f9;
        }

        .dark .ts-comment-body {
            color: #cbd5e1;
        }

        .dark .ts-textarea {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        .dark .ts-empty-box {
            background-color: #0f172a;
            border-color: #334155;
        }

        .dark .ts-empty-title {
            color: #cbd5e1;
        }

        .dark .ts-input-section {
            border-color: #334155;
        }
    </style>

    {{-- DAFTAR KOMENTAR (REAL-TIME POLLING 15 DETIK, HANYA SAAT TAB AKTIF) --}}
    <div class="ts-comments-list" wire:poll.visible.15s>
        @forelse ($comments as $comment)
            @php
                $isMe = auth()->id() === $comment->user_id;
                $user = $comment->user;
                $roleLabel = match (true) {
                    $user?->isSuperAdmin() => '👑 Super Admin',
                    $user?->isAdmin() => '🛡️ Admin',
                    $user?->isMedsosPlanner() => '📋 Planner',
                    $user?->isMedsosEditor() => '🎨 Editor',
                    $user?->isMedsosAdminPlatform() => '🚀 Admin Platform',
                    $user?->isInstruktur() => '👨‍🏫 Instruktur',
                    $user?->isStaff() => '💼 Staf',
                    default => '👥 Pegawai',
                };
                $roleClass = match (true) {
                    $user?->isSuperAdmin() => 'ts-role-admin',
                    $user?->isAdmin() => 'ts-role-admin',
                    $user?->isMedsosPlanner() => 'ts-role-planner',
                    $user?->isMedsosEditor() => 'ts-role-editor',
                    $user?->isMedsosAdminPlatform() => 'ts-role-admin-platform',
                    $user?->isInstruktur() => 'ts-role-planner',
                    $user?->isStaff() => 'ts-role-staff',
                    default => 'ts-role-user',
                };

                $avatarColors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#db2777', '#7c3aed'];
                $avatarColor = $avatarColors[abs(crc32($comment->user?->name ?? 'User')) % count($avatarColors)];
                $initial = strtoupper(mb_substr($comment->user?->name ?? 'U', 0, 1));
            @endphp
            <div class="ts-comment-card {{ $isMe ? 'is-me' : 'is-other' }}">
                <div class="ts-comment-header">
                    <div class="ts-author-info">
                        <span class="ts-avatar" style="background-color: {{ $avatarColor }};">
                            {{ $initial }}
                        </span>
                        <span class="ts-author-name">
                            {{ $comment->user?->name ?? 'Pengguna' }}
                        </span>
                        <span class="ts-role-badge {{ $roleClass }}">
                            {{ $roleLabel }}
                        </span>
                        <span class="ts-time">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if ($isMe || auth()->user()?->isAdmin())
                        <button type="button" wire:click="deleteComment({{ $comment->id }})"
                            wire:confirm="Yakin ingin menghapus komentar ini?" class="ts-delete-btn"
                            title="Hapus komentar">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="ts-comment-body">{{ $comment->body }}</div>
            </div>
        @empty
            <div class="ts-empty-box">
                <div class="ts-empty-icon">💬</div>
                <div class="ts-empty-title">Belum ada obrolan tim</div>
                <div class="ts-empty-sub">Mulai diskusi internal terkait brief, revisi aset, atau catatan postingan di
                    bawah.</div>
            </div>
        @endforelse
    </div>

    {{-- FORM INPUT KOMENTAR --}}
    @if ($contentId)
        <div class="ts-input-section">
            <div class="ts-input-row">
                <div style="flex: 1;">
                    <textarea wire:model="newComment" wire:keydown.enter.exact.prevent="addComment" rows="2"
                        placeholder="Tulis catatan atau pesan ke tim... (Enter untuk kirim)" class="ts-textarea"></textarea>
                </div>
                <button type="button" wire:click="addComment" wire:loading.attr="disabled" wire:target="addComment"
                    class="ts-send-button" title="Kirim Komentar (Enter)">
                    {{-- Ikon pesawat: tampil saat idle --}}
                    <span wire:loading.remove wire:target="addComment"
                        style="display: inline-flex; align-items: center;">
                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </span>
                    {{-- Spinner loading: HANYA tampil saat addComment berjalan --}}
                    <span wire:loading.inline-flex wire:target="addComment" class="ts-btn-loading-icon">
                        <svg class="ts-spinner-svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path style="opacity: 0.85;" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="addComment">Kirim</span>
                    <span wire:loading wire:target="addComment">Mengirim...</span>
                </button>
            </div>

            @error('newComment')
                <div class="ts-error-text">{{ $message }}</div>
            @enderror

            <div class="ts-helper-text">
                <span>💡 <strong>Enter</strong> kirim &bull; <strong>Shift + Enter</strong> baris baru</span>
                <span>🔒 Khusus Tim Internal</span>
            </div>
        </div>
    @else
        <div class="ts-alert-draft">
            ℹ️ Ruang diskusi & komentar tim akan aktif setelah konten ini dibuat dan disimpan pertama kali.
        </div>
    @endif
</div>
