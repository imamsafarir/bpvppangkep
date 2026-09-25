<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Tables;

use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->modifyQueryUsing(function (Builder $query) {
                $userId = Auth::id();
                $query->with([
                    'pegawai',
                    'instruktur',
                    'planner',
                    'editor',
                    'admin',
                    'platforms',
                    'reads' => fn($q) => $q->where('user_id', $userId),
                    'comments',
                ])->withCount('comments');

                $user = Auth::user();

                if (! $user) {
                    return $query->whereRaw('1 = 0');
                }

                // Super Admin atau Tim Medsos dapat melihat semua konten
                if ($user->isAdmin() || $user->isMedsosTeam()) {
                    return $query;
                }

                // User biasa / staf balai hanya melihat konten yang dibuat sendiri
                return $query->where(function (Builder $q) use ($user) {
                    $q->where('pegawai_id', $user->id)
                        ->orWhere('instruktur_id', $user->id)
                        ->orWhere('planner_id', $user->id);
                });
            })

            ->defaultSort('created_at', 'desc')

            ->recordUrl(function ($record) {
                if ($record->status === 'selesai' && ! (Auth::user()?->isAdmin() ?? false)) {
                    return ContentResource::getUrl('view', ['record' => $record]);
                }

                if (! ContentResource::canEdit($record)) {
                    return ContentResource::getUrl('view', ['record' => $record]);
                }

                return ContentResource::getUrl('edit', ['record' => $record]);
            })

            ->columns([
                // 1. Nomor Urut
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('50px'),

                // 2. Judul Konten & Informasi Pendukung
                TextColumn::make('nama_kegiatan')
                    ->label('Judul Konten')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->description(function ($record) {
                        $isFinal = $record->jenis_konten === 'final';
                        $typeBg = $isFinal ? '#ecfdf5' : '#fffbeb';
                        $typeColor = $isFinal ? '#065f46' : '#92400e';
                        $typeBorder = $isFinal ? '#a7f3d0' : '#fde68a';
                        $typeText = $isFinal ? '✨ Konten Siap' : '📦 Bahan Mentah';

                        $html = "<span style='display: inline-flex; align-items: center; gap: 5px; margin-top: 4px; flex-wrap: wrap;'>";
                        $html .= "<span style='font-size: 9.5px; font-weight: 700; padding: 1.5px 7px; border-radius: 9999px; background: {$typeBg}; color: {$typeColor}; border: 1px solid {$typeBorder};'>{$typeText}</span>";

                        $commentsCount = $record->comments_count ?? $record->comments->count();
                        if ($commentsCount > 0) {
                            $unreadCount = $record->getUnreadCommentsCount();

                            if ($unreadCount > 0) {
                                $html .= "<span style='font-size: 9.5px; font-weight: 700; padding: 1.5px 7px; border-radius: 9999px; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.15);'>";
                                $html .= "<span style='width: 6px; height: 6px; border-radius: 9999px; background: #ef4444; display: inline-block; box-shadow: 0 0 4px rgba(239, 68, 68, 0.6);'></span>";
                                $html .= "💬 {$commentsCount} Diskusi <span style='background: #ef4444; color: #ffffff; font-size: 8.5px; padding: 0 4px; border-radius: 9999px; font-weight: 800; letter-spacing: -0.02em;'>+{$unreadCount} baru</span>";
                                $html .= "</span>";
                            } else {
                                $html .= "<span style='font-size: 9.5px; font-weight: 700; padding: 1.5px 6px; border-radius: 9999px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;'>💬 {$commentsCount} Diskusi</span>";
                            }
                        }

                        $html .= "</span>";

                        return new HtmlString($html);
                    }),

                // 3. Platform Target
                TextColumn::make('platforms.name')
                    ->label('Platform')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower(trim($state))) {
                        'instagram', 'youtube' => 'danger',
                        'facebook' => 'info',
                        'tiktok' => 'gray',
                        'twitter / x', 'twitter', 'x' => 'gray',
                        default => 'primary',
                    })
                    ->separator(' ')
                    ->wrap(),

                // 4. Tim Penanggung Jawab & Petugas Aktif (Modern PIC Profile Capsule)
                TextColumn::make('petugas_aktif')
                    ->label('Petugas (PIC)')
                    ->html()
                    ->state(function ($record) {
                        $getInitials = static function (?string $name): string {
                            if (! $name || $name === '-' || $name === 'Belum Ditugaskan') {
                                return '?';
                            }
                            $words = preg_split('/\s+/', trim($name));
                            if (count($words) >= 2) {
                                return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                            }
                            return strtoupper(mb_substr($words[0], 0, 2));
                        };

                        $config = match ($record->status) {
                            'draft' => [
                                'name' => $record->instruktur?->name ?? $record->pegawai?->name ?? 'Belum Ditentukan',
                                'is_assigned' => filled($record->instruktur_id ?? $record->pegawai_id),
                                'role_icon' => $record->instruktur_id ? '👨‍🏫' : '💼',
                                'role_title' => $record->instruktur_id ? 'Instruktur' : 'Pengusul',
                                'sub_info' => 'Tahap Konsep',
                                'stage_tag' => 'DRAFT',
                                'avatar_bg' => '#3b82f6',
                                'card_bg' => '#eff6ff',
                                'card_border' => '#bfdbfe',
                                'text_color' => '#1e3a8a',
                                'sub_color' => '#2563eb',
                                'pill_bg' => '#dbeafe',
                                'pill_color' => '#1d4ed8',
                                'dot_color' => '#3b82f6',
                            ],
                            'menunggu_editor' => [
                                'name' => $record->editor?->name ?? 'Menunggu Editor',
                                'is_assigned' => filled($record->editor_id),
                                'role_icon' => '🎨',
                                'role_title' => 'Editor',
                                'sub_info' => ($record->instruktur?->name ?? $record->pegawai?->name) ? 'Bahan: ' . ($record->instruktur?->name ?? $record->pegawai?->name) : 'Antrean Editing',
                                'stage_tag' => filled($record->editor_id) ? 'EDITING' : 'ANTREAN',
                                'avatar_bg' => filled($record->editor_id) ? '#10b981' : '#f59e0b',
                                'card_bg' => filled($record->editor_id) ? '#f0fdf4' : '#fffbeb',
                                'card_border' => filled($record->editor_id) ? '#bbf7d0' : '#fde68a',
                                'text_color' => filled($record->editor_id) ? '#064e3b' : '#78350f',
                                'sub_color' => filled($record->editor_id) ? '#059669' : '#d97706',
                                'pill_bg' => filled($record->editor_id) ? '#dcfce7' : '#fef3c7',
                                'pill_color' => filled($record->editor_id) ? '#15803d' : '#b45309',
                                'dot_color' => filled($record->editor_id) ? '#10b981' : '#f59e0b',
                            ],
                            'revisi_editor' => [
                                'name' => $record->editor?->name ?? 'Editor Belum Ada',
                                'is_assigned' => filled($record->editor_id),
                                'role_icon' => '🔄',
                                'role_title' => 'Revisi Editor',
                                'sub_info' => 'Perbaikan Konten',
                                'stage_tag' => 'REVISI',
                                'avatar_bg' => '#ef4444',
                                'card_bg' => '#fff1f2',
                                'card_border' => '#fecdd3',
                                'text_color' => '#881337',
                                'sub_color' => '#e11d48',
                                'pill_bg' => '#ffe4e6',
                                'pill_color' => '#be123c',
                                'dot_color' => '#ef4444',
                            ],
                            'revisi_planner' => [
                                'name' => $record->planner?->name ?? 'Planner Belum Ada',
                                'is_assigned' => filled($record->planner_id),
                                'role_icon' => '🔄',
                                'role_title' => 'Revisi Planner',
                                'sub_info' => 'Perbaikan Konsep',
                                'stage_tag' => 'REVISI',
                                'avatar_bg' => '#ef4444',
                                'card_bg' => '#fff1f2',
                                'card_border' => '#fecdd3',
                                'text_color' => '#881337',
                                'sub_color' => '#e11d48',
                                'pill_bg' => '#ffe4e6',
                                'pill_color' => '#be123c',
                                'dot_color' => '#ef4444',
                            ],
                            'siap_publish' => [
                                'name' => $record->admin?->name ?? 'Admin Platform',
                                'is_assigned' => filled($record->admin_id),
                                'role_icon' => '🚀',
                                'role_title' => 'Siap Publish',
                                'sub_info' => $record->editor?->name ? 'Editor: ' . $record->editor->name : 'Siap Ditayangkan',
                                'stage_tag' => 'PUBLISH',
                                'avatar_bg' => '#8b5cf6',
                                'card_bg' => '#faf5ff',
                                'card_border' => '#e9d5ff',
                                'text_color' => '#581c87',
                                'sub_color' => '#7c3aed',
                                'pill_bg' => '#f3e8ff',
                                'pill_color' => '#6d28d9',
                                'dot_color' => '#8b5cf6',
                            ],
                            'selesai' => [
                                'name' => $record->admin?->name ?? $record->editor?->name ?? 'Tim Medsos',
                                'is_assigned' => true,
                                'role_icon' => '✅',
                                'role_title' => 'Tayang (Live)',
                                'sub_info' => $record->tanggal_posting ? 'Live: ' . Carbon::parse($record->tanggal_posting)->format('d M Y') : 'Konten Selesai',
                                'stage_tag' => 'LIVE',
                                'avatar_bg' => '#0d9488',
                                'card_bg' => '#f0fdfa',
                                'card_border' => '#99f6e4',
                                'text_color' => '#134e4a',
                                'sub_color' => '#0f766e',
                                'pill_bg' => '#ccfbf1',
                                'pill_color' => '#0f766e',
                                'dot_color' => '#0d9488',
                            ],
                            default => [
                                'name' => '-',
                                'is_assigned' => false,
                                'role_icon' => '👥',
                                'role_title' => 'Petugas',
                                'sub_info' => '-',
                                'stage_tag' => 'UNKNOWN',
                                'avatar_bg' => '#64748b',
                                'card_bg' => '#f8fafc',
                                'card_border' => '#e2e8f0',
                                'text_color' => '#334155',
                                'sub_color' => '#64748b',
                                'pill_bg' => '#f1f5f9',
                                'pill_color' => '#475569',
                                'dot_color' => '#94a3b8',
                            ],
                        };

                        $name = htmlspecialchars($config['name']);
                        $initials = $config['is_assigned'] ? $getInitials($config['name']) : $config['role_icon'];
                        $subInfo = htmlspecialchars($config['sub_info']);
                        $roleTitle = htmlspecialchars($config['role_title']);
                        $roleIcon = $config['role_icon'];
                        $stageTag = $config['stage_tag'];

                        // Alur tim lengkap untuk tooltip
                        $konseptor = ($record->instruktur?->name ?? $record->pegawai?->name ?? '-') . ($record->instruktur_id ? ' (Instruktur)' : ' (Pengusul)');
                        $planner = $record->planner?->name ?? '-';
                        $editor = $record->editor?->name ?? '-';
                        $admin = $record->admin?->name ?? '-';
                        $tooltipText = htmlspecialchars("Alur Tim:\n• Konsep: {$konseptor}\n• Planner: {$planner}\n• Editor: {$editor}\n• Admin: {$admin}");

                        $avatarContent = $config['is_assigned']
                            ? $initials
                            : "<span style='font-size: 13px; line-height: 1;'>{$initials}</span>";

                        $borderStyle = $config['is_assigned'] ? 'solid' : 'dashed';

                        return new HtmlString("
                            <div title='{$tooltipText}' onmouseover=\"this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 10px -2px rgba(0,0,0,0.08)';\" onmouseout=\"this.style.transform='none'; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.03)';\" style='font-family: inherit; display: inline-flex; align-items: center; gap: 8px; padding: 4px 10px 4px 5px; border-radius: 9999px; background: {$config['card_bg']}; border: 1px {$borderStyle} {$config['card_border']}; max-width: 270px; text-decoration: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 1px 2px rgba(0,0,0,0.03); cursor: default;'>
                                <div style='width: 26px; height: 26px; border-radius: 9999px; background: {$config['avatar_bg']}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10.5px; font-weight: 800; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.12);'>
                                    {$avatarContent}
                                </div>
                                <div style='display: flex; flex-direction: column; min-width: 0; line-height: 1.15; text-align: left;'>
                                    <div style='display: flex; align-items: center; gap: 5px;'>
                                        <span style='width: 6px; height: 6px; border-radius: 9999px; background: {$config['dot_color']}; flex-shrink: 0;'></span>
                                        <span style='font-size: 12px; font-weight: 700; color: {$config['text_color']}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;'>
                                            {$name}
                                        </span>
                                    </div>
                                    <span style='font-size: 9.5px; font-weight: 600; color: {$config['sub_color']}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 135px; margin-top: 1px;'>
                                        {$roleIcon} {$roleTitle} • {$subInfo}
                                    </span>
                                </div>
                                <span style='margin-left: auto; font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.4px; padding: 2px 7px; border-radius: 9999px; background: {$config['pill_bg']}; color: {$config['pill_color']}; border: 1px solid {$config['card_border']}; white-space: nowrap; flex-shrink: 0;'>
                                    {$stageTag}
                                </span>
                            </div>
                        ");
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search) {
                            $query
                                ->whereHas('pegawai', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('instruktur', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('planner', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('editor', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('admin', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"));
                        });
                    })
                    ->sortable(false),

                // 5. Target Deadline Konten
                TextColumn::make('tanggal_kegiatan')
                    ->label('Deadline')
                    ->sortable()
                    ->html()
                    ->state(function ($record) {
                        if (! $record->tanggal_kegiatan) {
                            return new HtmlString("<span style='color: #94a3b8; font-size: 12px;'>-</span>");
                        }

                        $target = Carbon::parse($record->tanggal_kegiatan)->startOfDay();
                        $today = Carbon::now()->startOfDay();
                        $diffDays = (int) $today->diffInDays($target, false);
                        $dateFormatted = $target->format('d M Y');
                        $fullDate = $target->format('l, d F Y');

                        if ($record->status === 'selesai') {
                            $badgeBg = '#dcfce7';
                            $badgeColor = '#15803d';
                            $badgeBorder = '#bbf7d0';
                            $badgeText = '✅ Selesai';
                        } elseif ($diffDays < 0) {
                            $abs = abs($diffDays);
                            $badgeBg = '#fee2e2';
                            $badgeColor = '#b91c1c';
                            $badgeBorder = '#fecaca';
                            $badgeText = "⚠️ Lewat {$abs}h";
                        } elseif ($diffDays === 0) {
                            $badgeBg = '#ffedd5';
                            $badgeColor = '#c2410c';
                            $badgeBorder = '#fed7aa';
                            $badgeText = '🔥 Hari Ini';
                        } elseif ($diffDays === 1) {
                            $badgeBg = '#fef3c7';
                            $badgeColor = '#b45309';
                            $badgeBorder = '#fde68a';
                            $badgeText = '⏳ Besok';
                        } elseif ($diffDays <= 7) {
                            $badgeBg = '#eff6ff';
                            $badgeColor = '#1d4ed8';
                            $badgeBorder = '#bfdbfe';
                            $badgeText = "🗓️ {$diffDays}h lagi";
                        } else {
                            $badgeBg = '#f8fafc';
                            $badgeColor = '#475569';
                            $badgeBorder = '#e2e8f0';
                            $badgeText = "📅 {$diffDays}h lagi";
                        }

                        return new HtmlString("
                            <div title='Target Deadline: {$fullDate}' style='display: flex; flex-direction: column; align-items: flex-start; gap: 3px;'>
                                <span style='font-size: 12.5px; font-weight: 700; color: #1e293b; letter-spacing: -0.2px;'>{$dateFormatted}</span>
                                <span style='font-size: 9.5px; font-weight: 700; padding: 1.5px 7px; border-radius: 9999px; background: {$badgeBg}; color: {$badgeColor}; border: 1px solid {$badgeBorder}; white-space: nowrap;'>{$badgeText}</span>
                            </div>
                        ");
                    }),

                // 6. Waktu Posting / Publikasi
                TextColumn::make('tanggal_posting')
                    ->label('Waktu Posting')
                    ->sortable()
                    ->html()
                    ->state(function ($record) {
                        if ($record->tanggal_posting) {
                            $date = Carbon::parse($record->tanggal_posting);
                            $dateFormatted = $date->format('d M Y');
                            $fullDate = $date->format('l, d F Y');

                            return new HtmlString("
                                <div title='Dipublikasikan pada: {$fullDate}' style='display: flex; flex-direction: column; align-items: flex-start; gap: 3px;'>
                                    <span style='font-size: 12.5px; font-weight: 700; color: #047857; letter-spacing: -0.2px;'>{$dateFormatted}</span>
                                    <span style='font-size: 9.5px; font-weight: 700; padding: 1.5px 7px; border-radius: 9999px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; white-space: nowrap;'>🚀 Tayang Live</span>
                                </div>
                            ");
                        }

                        if ($record->status === 'siap_publish') {
                            return new HtmlString("
                                <div style='display: flex; flex-direction: column; align-items: flex-start; gap: 3px;'>
                                    <span style='font-size: 12px; font-weight: 600; color: #7c3aed;'>Menunggu Jadwal</span>
                                    <span style='font-size: 9.5px; font-weight: 700; padding: 1.5px 7px; border-radius: 9999px; background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; white-space: nowrap;'>📢 Siap Publish</span>
                                </div>
                            ");
                        }

                        return new HtmlString("
                            <div style='display: flex; flex-direction: column; align-items: flex-start; gap: 3px;'>
                                <span style='font-size: 12px; color: #94a3b8;'>-</span>
                                <span style='font-size: 9.5px; font-weight: 600; padding: 1.5px 7px; border-radius: 9999px; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; white-space: nowrap;'>⏳ Belum Posting</span>
                            </div>
                        ");
                    }),

                // 7. Tautan Aset & Rilis Sosmed
                TextColumn::make('tautan_aset')
                    ->label('Aset & Link')
                    ->html()
                    ->state(function ($record) {
                        $links = [];

                        // Bahan Mentah
                        if ($record->link_media_mentah) {
                            $links[] = "<a href='{$record->link_media_mentah}' target='_blank' title='Buka Bahan Mentah (Cloud / Drive)' style='font-family: inherit; font-size: 10.5px; font-weight: 700; padding: 2.5px 8px; border-radius: 9999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a; transition: all 0.15s ease;' onmouseover=\"this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.06)';\" onmouseout=\"this.style.transform='none'; this.style.boxShadow='none';\">📁 Bahan</a>";
                        }

                        // Hasil Edit Final
                        if ($record->link_hasil_edit) {
                            $links[] = "<a href='{$record->link_hasil_edit}' target='_blank' title='Buka Hasil Editing Final' style='font-family: inherit; font-size: 10.5px; font-weight: 700; padding: 2.5px 8px; border-radius: 9999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; transition: all 0.15s ease;' onmouseover=\"this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.06)';\" onmouseout=\"this.style.transform='none'; this.style.boxShadow='none';\">🎬 Hasil</a>";
                        }

                        // Postingan Live
                        if ($record->link_postingan) {
                            $links[] = "<a href='{$record->link_postingan}' target='_blank' title='Buka Postingan Live' style='font-family: inherit; font-size: 10.5px; font-weight: 700; padding: 2.5px 8px; border-radius: 9999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; transition: all 0.15s ease;' onmouseover=\"this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.06)';\" onmouseout=\"this.style.transform='none'; this.style.boxShadow='none';\">🚀 Live</a>";
                        }

                        if (empty($links)) {
                            return new HtmlString("<span style='font-size: 12px; color: #94a3b8;'>-</span>");
                        }

                        return new HtmlString("<div style='display: inline-flex; align-items: center; gap: 6px; flex-wrap: nowrap;'>" . implode('', $links) . "</div>");
                    }),
            ])

            ->recordActions([
                ActionGroup::make([
                    // 1. Kirim ke Editor
                    Action::make('kirim_editor')
                        ->label('Kirim ke Editor')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) => (Auth::user()?->isAdmin() || Auth::user()?->isMedsosPlanner())
                                && in_array($record->status, ['draft', 'revisi_planner'])
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'menunggu_editor',
                                'planner_id' => Auth::id(),
                            ]);

                            Notification::make()
                                ->title('Terkirim ke Editor')
                                ->body("Status berhasil diperbarui menjadi Menunggu Editor.")
                                ->success()
                                ->send();
                        }),

                    // 2. Kirim ke Admin Langsung (Bypass jika materi sudah final)
                    Action::make('kirim_admin_langsung')
                        ->label('Kirim Langsung ke Admin')
                        ->icon('heroicon-o-arrow-up-right')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) => (Auth::user()?->isAdmin() || Auth::user()?->isMedsosPlanner())
                                && ($record->status === 'revisi_planner' || ($record->status === 'draft' && $record->jenis_konten === 'final'))
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'siap_publish',
                                'planner_id' => Auth::id(),
                            ]);

                            Notification::make()
                                ->title('Terkirim Langsung ke Admin')
                                ->body("Konten '{$record->nama_kegiatan}' siap untuk dipublikasikan.")
                                ->success()
                                ->send();
                        }),

                    // 3. Submit ke Admin (oleh Editor)
                    Action::make('serahkan_admin')
                        ->label('Submit ke Admin')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) => (Auth::user()?->isAdmin() || Auth::user()?->isMedsosEditor())
                                && in_array($record->status, ['menunggu_editor', 'revisi_editor'])
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'siap_publish',
                                'editor_id' => Auth::id(),
                            ]);

                            Notification::make()
                                ->title('Berhasil submit ke Admin')
                                ->body("Konten '{$record->nama_kegiatan}' telah diserahkan ke Admin Platform.")
                                ->success()
                                ->send();
                        }),

                    // 4. Minta Revisi (oleh Admin Platform)
                    Action::make('revisi')
                        ->label('Minta Revisi')
                        ->icon('heroicon-o-arrow-path')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) => (Auth::user()?->isAdmin() || Auth::user()?->isMedsosAdminPlatform())
                                && in_array($record->status, ['siap_publish', 'menunggu_editor'])
                        )
                        ->form([
                            Select::make('target_revisi')
                                ->label('Target Revisi')
                                ->options([
                                    'planner' => 'Planner (Revisi Konsep / Bahan)',
                                    'editor' => 'Editor (Revisi Video / Desain)',
                                ])
                                ->required(),

                            Textarea::make('catatan')
                                ->label('Catatan Revisi')
                                ->required()
                                ->placeholder('Tuliskan detail perbaikan yang diinginkan...'),
                        ])
                        ->action(function (array $data, $record) {
                            $record->revisions()->create([
                                'user_id' => Auth::id(),
                                'catatan' => $data['catatan'],
                                'target_revisi' => $data['target_revisi'],
                            ]);

                            $status = $data['target_revisi'] === 'planner'
                                ? 'revisi_planner'
                                : 'revisi_editor';

                            $record->update([
                                'status' => $status,
                            ]);

                            Notification::make()
                                ->title('Permintaan Revisi Terkirim')
                                ->body("Status konten berhasil diperbarui menjadi {$status}.")
                                ->warning()
                                ->send();
                        }),

                    // 5. Tandai Selesai / Live (oleh Admin Platform)
                    Action::make('selesai')
                        ->label('Tandai Selesai / Live')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) => (Auth::user()?->isAdmin() || Auth::user()?->isMedsosAdminPlatform())
                                && $record->status === 'siap_publish'
                        )
                        ->form([
                            TextInput::make('link_postingan')
                                ->label('Link Postingan Live (Opsional)')
                                ->url()
                                ->placeholder('https://instagram.com/p/...')
                                ->default(fn($record) => $record->link_postingan),
                        ])
                        ->action(function (array $data, $record) {
                            $updateData = [
                                'status' => 'selesai',
                                'admin_id' => Auth::id(),
                                'tanggal_posting' => now()->format('Y-m-d'),
                            ];

                            if (! empty($data['link_postingan'])) {
                                $updateData['link_postingan'] = $data['link_postingan'];
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('🚀 Konten Live & Selesai!')
                                ->body("Konten '{$record->nama_kegiatan}' telah ditandai selesai.")
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Aksi Cepat')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),

                Action::make('diskusi')
                    ->label('')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color(fn($record) => $record->getUnreadCommentsCount() > 0 ? 'danger' : 'gray')
                    ->badge(function ($record) {
                        $unread = $record->getUnreadCommentsCount();
                        if ($unread > 0) {
                            return "+{$unread}";
                        }
                        $total = $record->comments_count ?? $record->comments->count();
                        return $total > 0 ? (string) $total : null;
                    })
                    ->badgeColor(fn($record) => $record->getUnreadCommentsCount() > 0 ? 'danger' : 'gray')
                    ->tooltip(function ($record) {
                        $unread = $record->getUnreadCommentsCount();
                        $total = $record->comments_count ?? $record->comments->count();
                        if ($unread > 0) {
                            return "💬 {$total} diskusi ({$unread} pesan baru belum dibaca)";
                        }
                        return $total > 0 ? "💬 {$total} diskusi tim" : 'Buka ruang diskusi';
                    })
                    ->modalHeading(fn($record) => '💬 Diskusi Tim: ' . $record->nama_kegiatan)
                    ->modalDescription('Ruang obrolan dan koordinasi tim media sosial untuk konten ini.')
                    ->modalWidth('xl')
                    ->modalContent(fn($record) => view('timsosmed::filament.components.content-comments-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                ViewAction::make()
                    ->hiddenLabel()
                    ->tooltip('Lihat Detail'),

                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Edit / Lanjutkan')
                    ->visible(
                        fn($record) =>
                        $record->status !== 'selesai'
                            || (Auth::user()?->isAdmin() ?? false)
                    ),

                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip('Hapus')
                    ->visible(
                        fn($record) => (Auth::user()?->isAdmin() ?? false)
                            || ((Auth::user()?->isMedsosPlanner() ?? false) && $record->status !== 'selesai')
                    ),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status Alur Kerja')
                    ->options([
                        'draft' => 'Draft',
                        'menunggu_editor' => 'Menunggu Editor',
                        'revisi_editor' => 'Revisi Editor',
                        'revisi_planner' => 'Revisi Planner',
                        'siap_publish' => 'Siap Publish',
                        'selesai' => 'Selesai / Live',
                    ]),

                SelectFilter::make('jenis_konten')
                    ->label('Jenis Konten')
                    ->options([
                        'bahan' => 'Konten Bahan (Perlu Edit)',
                        'final' => 'Konten Final (Siap Posting)',
                    ]),

                SelectFilter::make('platforms')
                    ->relationship('platforms', 'name')
                    ->multiple()
                    ->label('Platform Media Sosial'),

                SelectFilter::make('instruktur_id')
                    ->relationship('instruktur', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Instruktur / Pembuat Konsep'),

                SelectFilter::make('planner_id')
                    ->relationship('planner', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Medsos Planner'),

                SelectFilter::make('editor_id')
                    ->relationship('editor', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Medsos Editor'),

                SelectFilter::make('admin_id')
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Medsos Admin Platform'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('mark_as_done')
                        ->label('Tandai Selesai (Massal)')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update([
                            'status' => 'selesai',
                            'admin_id' => Auth::id(),
                            'tanggal_posting' => now()->format('Y-m-d'),
                        ])),
                ]),
            ])

            ->emptyStateHeading('Belum Ada Konten Terjadwal')
            ->emptyStateDescription('Mulai buat rencana konten sosial media baru atau kelola melalui Kalender Konten.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                Action::make('buka_kalender')
                    ->label('📅 Buka Kalender Konten')
                    ->url(fn() => CalendarPage::getUrl())
                    ->button(),
                Action::make('create_konten')
                    ->label('✨ Buat Konten Baru')
                    ->url(fn() => ContentResource::getUrl('create'))
                    ->color('gray'),
            ])

            ->striped();
    }
}
