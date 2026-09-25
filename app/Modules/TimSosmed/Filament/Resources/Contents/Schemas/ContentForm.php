<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Schemas;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Livewire\ContentComments;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =========================================================================
                // 1. SECTION CATATAN REVISI (Jika ada revisi aktif)
                // =========================================================================
                Section::make('⚠️ Catatan Revisi')
                    ->description('Mohon perhatikan instruksi perbaikan berikut.')
                    ->visible(fn($record) => $record && $record->revisions()->exists() && in_array($record->status, ['revisi_planner', 'revisi_editor']))
                    ->collapsible()
                    ->schema([
                        Repeater::make('revisions')
                            ->label(false)
                            ->relationship()
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 4])
                                    ->schema([
                                        TextInput::make('catatan')
                                            ->label('Pesan Perbaikan')
                                            ->columnSpan(['md' => 3]),

                                        TextInput::make('target_revisi')
                                            ->label('Ditujukan Ke')
                                            ->formatStateUsing(fn($state) => ucfirst($state))
                                            ->columnSpan(['md' => 1]),
                                    ]),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->disabled(),
                    ])
                    ->columnSpanFull(),

                // =========================================================================
                // 2. SECTION STATUS ALUR KERJA (Hanya Superadmin yang bisa ubah langsung)
                // =========================================================================
                Section::make()
                    ->schema([
                        ToggleButtons::make('status')
                            ->label(false)
                            ->disabled(fn() => ! (Auth::user()?->isAdmin() ?? false))
                            ->dehydrated(true)
                            ->options([
                                'draft' => 'Draft',
                                'menunggu_editor' => 'Menunggu Editor',
                                'revisi_editor' => 'Revisi Editor',
                                'revisi_planner' => 'Revisi Planner',
                                'siap_publish' => 'Siap Publish',
                                'selesai' => 'Selesai',
                            ])
                            ->icons([
                                'draft' => 'heroicon-m-pencil',
                                'menunggu_editor' => 'heroicon-m-clock',
                                'revisi_editor' => 'heroicon-m-exclamation-triangle',
                                'revisi_planner' => 'heroicon-m-arrow-path',
                                'siap_publish' => 'heroicon-m-eye',
                                'selesai' => 'heroicon-m-check-circle',
                            ])
                            ->colors([
                                'draft' => 'gray',
                                'menunggu_editor' => 'warning',
                                'revisi_editor' => 'danger',
                                'revisi_planner' => 'danger',
                                'siap_publish' => 'info',
                                'selesai' => 'success',
                            ])
                            ->default('draft')
                            ->required()
                            ->inline()
                            ->extraAttributes([
                                'style' => 'display: flex; justify-content: center; width: 100%;',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // =========================================================================
                // 3. GRID UTAMA (KIRI: 3 SECTION KERJA MENURUN | KANAN: PANEL DISKUSI STICKY)
                // =========================================================================
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([

                        // =================================================================
                        // KOLOM KIRI (LEBAR: 2/3) - 3 SECTION ALUR PEKERJAAN DROPDOWN
                        // =================================================================
                        Group::make()
                            ->schema([

                                // -----------------------------------------------------
                                // SECTION 1: PERENCANAAN KONTEN (Medsos Planner)
                                // -----------------------------------------------------
                                Section::make('1. Perencanaan Konten (Medsos Planner)')
                                    ->description('Informasi dasar kegiatan dan tautan bahan mentah.')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->collapsible()
                                    ->headerActions([
                                        Action::make('kirim_editor')
                                            ->label('Kirim ke Editor')
                                            ->icon('heroicon-m-paper-airplane')
                                            ->color('warning')
                                            ->button()
                                            ->requiresConfirmation()
                                            ->visible(
                                                fn($record) => $record
                                                    && in_array($record->status, ['draft', 'revisi_planner'])
                                                    && (Auth::user()?->isAdmin() || Auth::user()?->isMedsosPlanner())
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                $record && $record->status === 'selesai' && ! (Auth::user()?->isAdmin() ?? false)
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'menunggu_editor',
                                                    'planner_id' => Auth::id(),
                                                ]);

                                                $livewire->js("
                                                    if (window.parent && window.parent !== window) {
                                                        window.parent.postMessage({ type: 'content-saved' }, '*');
                                                    }
                                                ");

                                                Notification::make()
                                                    ->title('Konten Terkirim ke Editor')
                                                    ->body('Status berhasil diperbarui menjadi Menunggu Editor.')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => CalendarPage::getUrl()),

                                        Action::make('kirim_admin_langsung')
                                            ->label('Kirim ke Admin Langsung')
                                            ->icon('heroicon-m-shield-check')
                                            ->color('success')
                                            ->button()
                                            ->requiresConfirmation()
                                            ->visible(
                                                fn($record) => $record
                                                    && $record->status === 'revisi_planner'
                                                    && (Auth::user()?->isAdmin() || Auth::user()?->isMedsosPlanner())
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                $record && $record->status === 'selesai' && ! (Auth::user()?->isAdmin() ?? false)
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'siap_publish',
                                                    'planner_id' => Auth::id(),
                                                ]);

                                                $livewire->js("
                                                    if (window.parent && window.parent !== window) {
                                                        window.parent.postMessage({ type: 'content-saved' }, '*');
                                                    }
                                                ");

                                                Notification::make()
                                                    ->title('Konten Terkirim Langsung ke Admin')
                                                    ->body('Status berhasil diperbarui menjadi Siap Publish.')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => CalendarPage::getUrl()),
                                    ])
                                    ->schema([
                                        Placeholder::make('info_penanggung_jawab')
                                            ->label('Tim Bertugas (Tahap 1: Perencanaan)')
                                            ->content(function ($record) {
                                                $css = "
                                                    <style>
                                                    .ts-card-grid {
                                                        display: grid !important;
                                                        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
                                                        gap: 12px !important;
                                                        width: 100% !important;
                                                    }
                                                    .ts-user-card {
                                                        display: flex !important;
                                                        align-items: center !important;
                                                        justify-content: space-between !important;
                                                        padding: 12px 16px !important;
                                                        border-radius: 12px !important;
                                                        border-width: 1px !important;
                                                        border-style: solid !important;
                                                        box-sizing: border-box !important;
                                                    }
                                                    .ts-user-card-inner {
                                                        display: flex !important;
                                                        align-items: center !important;
                                                        gap: 12px !important;
                                                    }
                                                    .ts-user-card-icon {
                                                        font-size: 22px !important;
                                                        line-height: 1 !important;
                                                        flex-shrink: 0 !important;
                                                    }
                                                    .ts-user-card-title {
                                                        font-size: 11px !important;
                                                        font-weight: 700 !important;
                                                        text-transform: uppercase !important;
                                                        letter-spacing: 0.5px !important;
                                                        line-height: 1.2 !important;
                                                        margin-bottom: 2px !important;
                                                    }
                                                    .ts-user-card-name {
                                                        font-size: 14px !important;
                                                        font-weight: 700 !important;
                                                        line-height: 1.2 !important;
                                                    }
                                                    .ts-user-card-pill {
                                                        font-size: 10px !important;
                                                        font-weight: 700 !important;
                                                        padding: 3px 10px !important;
                                                        border-radius: 9999px !important;
                                                        white-space: nowrap !important;
                                                        flex-shrink: 0 !important;
                                                        letter-spacing: 0.3px !important;
                                                    }
                                                    /* Themes */
                                                    .ts-card-blue {
                                                        background: #eff6ff !important;
                                                        border-color: #bfdbfe !important;
                                                    }
                                                    .ts-card-blue .ts-user-card-title { color: #2563eb !important; }
                                                    .ts-card-blue .ts-user-card-name { color: #1e3a8a !important; }
                                                    .ts-card-blue .ts-user-card-pill { background: #dbeafe !important; color: #1d4ed8 !important; }

                                                    .ts-card-gray {
                                                        background: #f8fafc !important;
                                                        border-color: #e2e8f0 !important;
                                                    }
                                                    .ts-card-gray .ts-user-card-title { color: #64748b !important; }
                                                    .ts-card-gray .ts-user-card-name { color: #1e293b !important; }
                                                    .ts-card-gray .ts-user-card-pill { background: #e2e8f0 !important; color: #475569 !important; }

                                                    .ts-card-amber {
                                                        background: #fffbeb !important;
                                                        border-color: #fde68a !important;
                                                    }
                                                    .ts-card-amber .ts-user-card-title { color: #d97706 !important; }
                                                    .ts-card-amber .ts-user-card-name { color: #78350f !important; }
                                                    .ts-card-amber .ts-user-card-pill { background: #fef3c7 !important; color: #b45309 !important; }

                                                    .ts-card-emerald {
                                                        background: #ecfdf5 !important;
                                                        border-color: #a7f3d0 !important;
                                                    }
                                                    .ts-card-emerald .ts-user-card-title { color: #059669 !important; }
                                                    .ts-card-emerald .ts-user-card-name { color: #064e3b !important; }
                                                    .ts-card-emerald .ts-user-card-pill { background: #d1fae5 !important; color: #047857 !important; }

                                                    .ts-card-purple {
                                                        background: #faf5ff !important;
                                                        border-color: #e9d5ff !important;
                                                    }
                                                    .ts-card-purple .ts-user-card-title { color: #7c3aed !important; }
                                                    .ts-card-purple .ts-user-card-name { color: #581c87 !important; }
                                                    .ts-card-purple .ts-user-card-pill { background: #f3e8ff !important; color: #6d28d9 !important; }

                                                    .ts-card-dashed {
                                                        background: #f8fafc !important;
                                                        border-color: #cbd5e1 !important;
                                                        border-style: dashed !important;
                                                    }
                                                    .ts-card-dashed .ts-user-card-title { color: #94a3b8 !important; }
                                                    .ts-card-dashed .ts-user-card-name { color: #94a3b8 !important; font-style: italic !important; font-weight: 500 !important; font-size: 13px !important; }

                                                    /* Dark Mode */
                                                    .dark .ts-card-blue {
                                                        background: rgba(30, 58, 138, 0.25) !important;
                                                        border-color: rgba(59, 130, 246, 0.35) !important;
                                                    }
                                                    .dark .ts-card-blue .ts-user-card-title { color: #60a5fa !important; }
                                                    .dark .ts-card-blue .ts-user-card-name { color: #e0f2fe !important; }
                                                    .dark .ts-card-blue .ts-user-card-pill { background: rgba(59, 130, 246, 0.25) !important; color: #93c5fd !important; }

                                                    .dark .ts-card-gray {
                                                        background: rgba(51, 65, 85, 0.25) !important;
                                                        border-color: rgba(100, 116, 139, 0.35) !important;
                                                    }
                                                    .dark .ts-card-gray .ts-user-card-title { color: #94a3b8 !important; }
                                                    .dark .ts-card-gray .ts-user-card-name { color: #f1f5f9 !important; }
                                                    .dark .ts-card-gray .ts-user-card-pill { background: rgba(100, 116, 139, 0.3) !important; color: #cbd5e1 !important; }

                                                    .dark .ts-card-amber {
                                                        background: rgba(120, 53, 15, 0.25) !important;
                                                        border-color: rgba(245, 158, 11, 0.35) !important;
                                                    }
                                                    .dark .ts-card-amber .ts-user-card-title { color: #fbbf24 !important; }
                                                    .dark .ts-card-amber .ts-user-card-name { color: #fef3c7 !important; }
                                                    .dark .ts-card-amber .ts-user-card-pill { background: rgba(245, 158, 11, 0.25) !important; color: #fde68a !important; }

                                                    .dark .ts-card-emerald {
                                                        background: rgba(6, 78, 59, 0.25) !important;
                                                        border-color: rgba(16, 185, 129, 0.35) !important;
                                                    }
                                                    .dark .ts-card-emerald .ts-user-card-title { color: #34d399 !important; }
                                                    .dark .ts-card-emerald .ts-user-card-name { color: #d1fae5 !important; }
                                                    .dark .ts-card-emerald .ts-user-card-pill { background: rgba(16, 185, 129, 0.25) !important; color: #6ee7b7 !important; }

                                                    .dark .ts-card-purple {
                                                        background: rgba(88, 28, 135, 0.25) !important;
                                                        border-color: rgba(147, 51, 234, 0.35) !important;
                                                    }
                                                    .dark .ts-card-purple .ts-user-card-title { color: #c084fc !important; }
                                                    .dark .ts-card-purple .ts-user-card-name { color: #f3e8ff !important; }
                                                    .dark .ts-card-purple .ts-user-card-pill { background: rgba(147, 51, 234, 0.25) !important; color: #d8b4fe !important; }

                                                    .dark .ts-card-dashed {
                                                        background: rgba(30, 41, 59, 0.2) !important;
                                                        border-color: #475569 !important;
                                                    }
                                                    </style>
                                                ";

                                                if (! $record) {
                                                    $user = Auth::user();
                                                    $roles = [];
                                                    if ($user?->isAdmin()) $roles[] = '👑 Administrator';
                                                    if ($user?->isInstruktur()) $roles[] = '👨‍🏫 Medsos Instruktur';
                                                    if ($user?->isMedsosPlanner()) $roles[] = '📋 Medsos Planner';
                                                    if ($user?->isMedsosEditor()) $roles[] = '🎨 Medsos Editor';
                                                    if ($user?->isMedsosAdminPlatform()) $roles[] = '🚀 Medsos Admin Platform';
                                                    if ($user?->isStaff()) $roles[] = '💼 Staf Balai';
                                                    if (empty($roles)) $roles[] = '👥 Pengguna';

                                                    $roleLabel = implode(', ', $roles);
                                                    $userName = $user?->name ?? 'User';

                                                    return new HtmlString("
                                                        {$css}
                                                        <div class='ts-user-card ts-card-blue' style='display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px; width: 100%;'>
                                                            <div class='ts-user-card-inner' style='display: flex; align-items: center; gap: 12px;'>
                                                                <span class='ts-user-card-icon' style='font-size: 22px;'>👤</span>
                                                                <div>
                                                                    <div class='ts-user-card-title'>Dibuat Oleh</div>
                                                                    <div class='ts-user-card-name'>{$userName}</div>
                                                                </div>
                                                            </div>
                                                            <span class='ts-user-card-pill'>{$roleLabel}</span>
                                                        </div>
                                                    ");
                                                }

                                                $record = $record->fresh([
                                                    'pegawai',
                                                    'instruktur',
                                                    'planner',
                                                ]);

                                                $html = "{$css}<div class='ts-card-grid' style='display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; width: 100%;'>";

                                                // 1. Instruktur / Konseptor
                                                if ($record->instruktur) {
                                                    $instrukturName = htmlspecialchars($record->instruktur->name);
                                                    $html .= "
                                                        <div class='ts-user-card ts-card-blue' style='display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px;'>
                                                            <div class='ts-user-card-inner' style='display: flex; align-items: center; gap: 12px;'>
                                                                <span class='ts-user-card-icon' style='font-size: 22px;'>👨‍🏫</span>
                                                                <div>
                                                                    <div class='ts-user-card-title'>Instruktur</div>
                                                                    <div class='ts-user-card-name'>{$instrukturName}</div>
                                                                </div>
                                                            </div>
                                                            <span class='ts-user-card-pill'>Pembuat Konsep</span>
                                                        </div>";
                                                } elseif ($record->pegawai) {
                                                    $pegawaiName = htmlspecialchars($record->pegawai->name);
                                                    $html .= "
                                                        <div class='ts-user-card ts-card-gray' style='display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px;'>
                                                            <div class='ts-user-card-inner' style='display: flex; align-items: center; gap: 12px;'>
                                                                <span class='ts-user-card-icon' style='font-size: 22px;'>💼</span>
                                                                <div>
                                                                    <div class='ts-user-card-title'>Staf Pengusul</div>
                                                                    <div class='ts-user-card-name'>{$pegawaiName}</div>
                                                                </div>
                                                            </div>
                                                            <span class='ts-user-card-pill'>Pengusul</span>
                                                        </div>";
                                                }

                                                // 2. Medsos Planner
                                                if ($record->planner) {
                                                    $plannerName = htmlspecialchars($record->planner->name);
                                                    $html .= "
                                                        <div class='ts-user-card ts-card-amber' style='display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px;'>
                                                            <div class='ts-user-card-inner' style='display: flex; align-items: center; gap: 12px;'>
                                                                <span class='ts-user-card-icon' style='font-size: 22px;'>📋</span>
                                                                <div>
                                                                    <div class='ts-user-card-title'>Medsos Planner</div>
                                                                    <div class='ts-user-card-name'>{$plannerName}</div>
                                                                </div>
                                                            </div>
                                                            <span class='ts-user-card-pill'>Penanggung Jawab</span>
                                                        </div>";
                                                } else {
                                                    $html .= "
                                                        <div class='ts-user-card ts-card-dashed' style='display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px;'>
                                                            <span class='ts-user-card-icon' style='font-size: 22px; opacity: 0.5;'>📋</span>
                                                            <div>
                                                                <div class='ts-user-card-title'>Medsos Planner</div>
                                                                <div class='ts-user-card-name'>Menunggu peninjauan planner...</div>
                                                            </div>
                                                        </div>";
                                                }

                                                $html .= "</div>";

                                                return new HtmlString($html);
                                            })
                                            ->columnSpanFull(),

                                        Grid::make(['default' => 1, 'md' => 2])
                                            ->schema([
                                                Hidden::make('pegawai_id')
                                                    ->default(fn() => Auth::id())
                                                    ->dehydrated(fn($state, $record) => blank($record?->pegawai_id) && filled($state)),

                                                Hidden::make('instruktur_id')
                                                    ->default(fn() => Auth::user()?->isInstruktur() ? Auth::id() : null)
                                                    ->dehydrated(fn($state, $record) => blank($record?->instruktur_id) && filled($state)),

                                                Hidden::make('planner_id')
                                                    ->default(fn() => Auth::user()?->isMedsosPlanner() ? Auth::id() : null)
                                                    ->dehydrated(fn($state, $record) => blank($record?->planner_id) && filled($state)),

                                                TextInput::make('nama_kegiatan')
                                                    ->label('Nama Kegiatan')
                                                    ->required()
                                                    ->maxLength(255),

                                                DatePicker::make('tanggal_kegiatan')
                                                    ->label('Tanggal Kegiatan')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('d F Y')
                                                    ->default(request()->query('tanggal_kegiatan'))
                                                    ->hintActions([
                                                        Action::make('today')
                                                            ->label('Hari Ini')
                                                            ->visible(fn() => Auth::user()?->isMedsosTeam() ?? false)
                                                            ->action(fn(Set $set) => $set('tanggal_kegiatan', now()->format('Y-m-d'))),

                                                        Action::make('tomorrow')
                                                            ->label('Besok')
                                                            ->visible(fn() => Auth::user()?->isMedsosTeam() ?? false)
                                                            ->action(fn(Set $set) => $set('tanggal_kegiatan', now()->addDay()->format('Y-m-d'))),

                                                        Action::make('nextWeek')
                                                            ->label('1 Minggu')
                                                            ->visible(fn() => Auth::user()?->isMedsosTeam() ?? false)
                                                            ->action(fn(Set $set) => $set('tanggal_kegiatan', now()->addWeek()->format('Y-m-d'))),
                                                    ]),

                                                Select::make('jenis_konten')
                                                    ->label('Jenis Konten')
                                                    ->options([
                                                        'bahan' => 'Konten Bahan (Perlu Diedit)',
                                                        'final' => 'Konten Final (Siap Posting)',
                                                    ])
                                                    ->required()
                                                    ->native(false)
                                                    ->live()
                                                    ->placeholder('Pilih jenis konten dahulu...'),

                                                Group::make()
                                                    ->schema([
                                                        RichEditor::make('brief')
                                                            ->label('Brief Konten (Instruksi)')
                                                            ->columnSpanFull(),

                                                        RichEditor::make('caption')
                                                            ->label('Caption Sosmed')
                                                            ->columnSpanFull(),

                                                        CheckboxList::make('platforms')
                                                            ->relationship('platforms', 'name')
                                                            ->columns(['default' => 2, 'lg' => 3])
                                                            ->columnSpanFull(),

                                                        TextInput::make('link_referensi')
                                                            ->url()
                                                            ->label('Link Referensi (Opsional)')
                                                            ->columnSpanFull()
                                                            ->copyable(copyMessage: 'Copied!', copyMessageDuration: 1500)
                                                            ->suffixAction(
                                                                Action::make('openLink')
                                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                                    ->color('info')
                                                                    ->tooltip('Buka di tab baru')
                                                                    ->url(fn($state) => $state)
                                                                    ->openUrlInNewTab()
                                                                    ->visible(fn($state) => filled($state))
                                                            ),

                                                        TextInput::make('link_media_mentah')
                                                            ->label('Link Google Drive / Nextcloud (Bahan Mentah)')
                                                            ->helperText('Masukkan link folder/file bahan mentah.')
                                                            ->url()
                                                            ->columnSpanFull()
                                                            ->copyable(copyMessage: 'Copied!', copyMessageDuration: 1500)
                                                            ->suffixAction(
                                                                Action::make('openLink')
                                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                                    ->color('info')
                                                                    ->tooltip('Buka di tab baru')
                                                                    ->url(fn($state) => $state)
                                                                    ->openUrlInNewTab()
                                                                    ->visible(fn($state) => filled($state))
                                                            ),

                                                        SpatieMediaLibraryFileUpload::make('bahan')
                                                            ->label('📁 Upload Bahan Mentah (Gambar/Video)')
                                                            ->helperText('Upload bisa lebih dari satu file. Gambar otomatis dikompresi ke AVIF untuk preview. Download = file asli.')
                                                            ->collection('bahan')
                                                            ->multiple()
                                                            ->reorderable()
                                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'])
                                                            ->maxSize(512000)
                                                            ->downloadable()
                                                            ->openable()
                                                            ->panelLayout('grid')
                                                            ->hintAction(
                                                                Action::make('download_semua_bahan')
                                                                    ->label('📦 Download Semua Bahan (ZIP)')
                                                                    ->icon('heroicon-m-arrow-down-tray')
                                                                    ->color('primary')
                                                                    ->visible(fn($record) => $record && $record->getMedia('bahan')->isNotEmpty())
                                                                    ->url(fn($record) => route('timsosmed.content.download-collection', ['content' => $record->id, 'collection' => 'bahan']))
                                                                    ->openUrlInNewTab()
                                                            )
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columnSpanFull()
                                                    ->columns(['default' => 1, 'md' => 2])
                                                    ->visible(fn(Get $get) => filled($get('jenis_konten'))),
                                            ]),
                                    ])
                                    ->disabled(function ($record) {
                                        $user = Auth::user();
                                        if (! $user) {
                                            return true;
                                        }
                                        if ($user->isAdmin()) {
                                            return false;
                                        }
                                        if ($record && $record->status === 'selesai') {
                                            return true;
                                        }
                                        // Instruktur yang bukan planner hanya bisa mengisi/mengedit saat draft / baru dibuat
                                        if ($user->isInstruktur() && ! $user->isMedsosPlanner()) {
                                            if ($record && $record->status !== 'draft') {
                                                return true;
                                            }
                                            return false;
                                        }
                                        return ! ($user->isMedsosPlanner() || $user->isStaff());
                                    }),

                                // -----------------------------------------------------
                                // SECTION 2: PRODUKSI & EDITING (Medsos Editor)
                                // -----------------------------------------------------
                                Section::make('2. Produksi & Editing (Medsos Editor)')
                                    ->description('Proses editor & tautan hasil editing final.')
                                    ->icon('heroicon-o-paint-brush')
                                    ->collapsible()
                                    ->headerActions([
                                        Action::make('serahkan_admin')
                                            ->label('Submit ke Admin')
                                            ->icon('heroicon-m-check-badge')
                                            ->color('success')
                                            ->button()
                                            ->requiresConfirmation()
                                            ->visible(
                                                fn($record) => $record
                                                    && in_array($record->status, ['menunggu_editor', 'revisi_editor'])
                                                    && (Auth::user()?->isAdmin() || Auth::user()?->isMedsosEditor())
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                $record && $record->status === 'selesai' && ! (Auth::user()?->isAdmin() ?? false)
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'siap_publish',
                                                    'editor_id' => Auth::id(),
                                                ]);

                                                $livewire->js("
                                                    if (window.parent && window.parent !== window) {
                                                        window.parent.postMessage({ type: 'content-saved' }, '*');
                                                    }
                                                ");

                                                Notification::make()
                                                    ->title('Berhasil submit ke Admin')
                                                    ->body("Konten '{$record->nama_kegiatan}' telah diserahkan ke Admin Platform.")
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => CalendarPage::getUrl()),
                                    ])
                                    ->schema([
                                        Placeholder::make('info_editor')
                                            ->label('Tim Bertugas (Tahap 2: Editing)')
                                            ->content(function ($record) {
                                                if (! $record) {
                                                    return new HtmlString("
                                                        <div class='ts-user-card ts-card-dashed' style='display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px;'>
                                                            <span class='ts-user-card-icon' style='font-size: 22px; opacity: 0.5;'>🎨</span>
                                                            <div>
                                                                <div class='ts-user-card-title'>Medsos Editor</div>
                                                                <div class='ts-user-card-name'>Menunggu konten dibuat...</div>
                                                            </div>
                                                        </div>");
                                                }

                                                if ($record->editor) {
                                                    $editorName = htmlspecialchars($record->editor->name);
                                                    return new HtmlString("
                                                        <div class='ts-user-card ts-card-emerald' style='display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px;'>
                                                            <div class='ts-user-card-inner' style='display: flex; align-items: center; gap: 12px;'>
                                                                <span class='ts-user-card-icon' style='font-size: 22px;'>🎨</span>
                                                                <div>
                                                                    <div class='ts-user-card-title'>Medsos Editor</div>
                                                                    <div class='ts-user-card-name'>{$editorName}</div>
                                                                </div>
                                                            </div>
                                                            <span class='ts-user-card-pill'>Editor Bertugas</span>
                                                        </div>");
                                                }

                                                return new HtmlString("
                                                    <div class='ts-user-card ts-card-dashed' style='display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px;'>
                                                        <span class='ts-user-card-icon' style='font-size: 22px; opacity: 0.5;'>🎨</span>
                                                        <div>
                                                            <div class='ts-user-card-title'>Medsos Editor</div>
                                                            <div class='ts-user-card-name'>Menunggu proses editing...</div>
                                                        </div>
                                                    </div>");
                                            })
                                            ->columnSpanFull(),

                                        TextInput::make('link_hasil_edit')
                                            ->label('Link Google Drive / Nextcloud (Hasil Final)')
                                            ->helperText('Editor masukkan link hasil video/konten yang sudah diedit di sini.')
                                            ->url()
                                            ->copyable(copyMessage: 'Copied!', copyMessageDuration: 1500)
                                            ->columnSpanFull()
                                            ->suffixAction(
                                                Action::make('openLink')
                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                    ->color('info')
                                                    ->tooltip('Buka di tab baru')
                                                    ->url(fn($state) => $state)
                                                    ->openUrlInNewTab()
                                                    ->visible(fn($state) => filled($state))
                                            ),

                                        SpatieMediaLibraryFileUpload::make('editing')
                                            ->label('🎬 Upload Hasil Editing (Gambar/Video Final)')
                                            ->helperText('Upload bisa lebih dari satu file. Gambar otomatis dikompresi ke AVIF untuk preview. Download = file asli.')
                                            ->collection('editing')
                                            ->multiple()
                                            ->reorderable()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'])
                                            ->maxSize(512000)
                                            ->downloadable()
                                            ->openable()
                                            ->panelLayout('grid')
                                            ->hintAction(
                                                Action::make('download_semua_editing')
                                                    ->label('📦 Download Semua Hasil Edit (ZIP)')
                                                    ->icon('heroicon-m-arrow-down-tray')
                                                    ->color('success')
                                                    ->visible(fn($record) => $record && $record->getMedia('editing')->isNotEmpty())
                                                    ->url(fn($record) => route('timsosmed.content.download-collection', ['content' => $record->id, 'collection' => 'editing']))
                                                    ->openUrlInNewTab()
                                            )
                                            ->columnSpanFull(),
                                    ])
                                    ->disabled(function ($record) {
                                        $user = Auth::user();
                                        if (! $user) {
                                            return true;
                                        }
                                        if ($user->isAdmin()) {
                                            return false;
                                        }
                                        if ($record && $record->status === 'selesai') {
                                            return true;
                                        }
                                        return ! $user->isMedsosEditor();
                                    }),

                                // -----------------------------------------------------
                                // SECTION 3: PUBLIKASI & DISTRIBUSI (Medsos Admin Platform)
                                // -----------------------------------------------------
                                Section::make('3. Publikasi & Distribusi (Medsos Admin Platform)')
                                    ->description('Tahap akhir publikasi dan link postingan.')
                                    ->icon('heroicon-o-globe-alt')
                                    ->collapsible()
                                    ->headerActions([
                                        Action::make('set_selesai')
                                            ->label('Tandai Selesai / Live')
                                            ->icon('heroicon-m-globe-alt')
                                            ->color('success')
                                            ->button()
                                            ->requiresConfirmation()
                                            ->visible(
                                                fn($record) => $record
                                                    && $record->status === 'siap_publish'
                                                    && (Auth::user()?->isAdmin() || Auth::user()?->isMedsosAdminPlatform())
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                $record && $record->status === 'selesai' && ! (Auth::user()?->isAdmin() ?? false)
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'selesai',
                                                    'admin_id' => Auth::id(),
                                                    'tanggal_posting' => now()->format('Y-m-d'),
                                                ]);

                                                $livewire->js("
                                                    if (window.parent && window.parent !== window) {
                                                        window.parent.postMessage({ type: 'content-saved' }, '*');
                                                    }
                                                ");

                                                Notification::make()
                                                    ->title('🚀 Konten Berhasil Dipublikasikan')
                                                    ->body("Konten '{$record->nama_kegiatan}' telah ditandai selesai dan live.")
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => CalendarPage::getUrl()),

                                        Action::make('minta_revisi')
                                            ->label('Minta Revisi')
                                            ->icon('heroicon-m-arrow-path')
                                            ->color('danger')
                                            ->button()
                                            ->requiresConfirmation()
                                            ->visible(
                                                fn($record) => $record
                                                    && in_array($record->status, ['siap_publish', 'menunggu_editor'])
                                                    && (Auth::user()?->isAdmin() || Auth::user()?->isMedsosAdminPlatform())
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                $record && $record->status === 'selesai' && ! (Auth::user()?->isAdmin() ?? false)
                                            )
                                            ->schema([
                                                Select::make('target_revisi')
                                                    ->label('Target Revisi')
                                                    ->options([
                                                        'editor' => 'Editor (Edit Ulang Video/Grafis)',
                                                        'planner' => 'Planner (Revisi Konsep/Aset Mentah)',
                                                    ])
                                                    ->required(),
                                                Textarea::make('catatan')
                                                    ->label('Catatan Revisi')
                                                    ->required(),
                                            ])
                                            ->action(function ($record, array $data, $livewire) {
                                                $record->revisions()->create([
                                                    'user_id' => Auth::id(),
                                                    'target_revisi' => $data['target_revisi'],
                                                    'catatan' => $data['catatan'],
                                                ]);

                                                $status = $data['target_revisi'] === 'planner'
                                                    ? 'revisi_planner'
                                                    : 'revisi_editor';

                                                $record->update(['status' => $status]);

                                                $livewire->js("
                                                    if (window.parent && window.parent !== window) {
                                                        window.parent.postMessage({ type: 'content-saved' }, '*');
                                                    }
                                                ");

                                                Notification::make()
                                                    ->title('Permintaan Revisi Terkirim')
                                                    ->warning()
                                                    ->send();

                                                $livewire->redirect(CalendarPage::getUrl());
                                            }),
                                    ])
                                    ->schema([
                                        Placeholder::make('info_admin')
                                            ->label('Tim Bertugas (Tahap 3: Publikasi)')
                                            ->content(function ($record) {
                                                if (! $record) {
                                                    return new HtmlString("
                                                        <div class='ts-user-card ts-card-dashed' style='display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px;'>
                                                            <span class='ts-user-card-icon' style='font-size: 22px; opacity: 0.5;'>🚀</span>
                                                            <div>
                                                                <div class='ts-user-card-title'>Medsos Admin Platform</div>
                                                                <div class='ts-user-card-name'>Menunggu konten dibuat...</div>
                                                            </div>
                                                        </div>");
                                                }

                                                if ($record->admin) {
                                                    $adminName = htmlspecialchars($record->admin->name);
                                                    return new HtmlString("
                                                        <div class='ts-user-card ts-card-purple' style='display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px;'>
                                                            <div class='ts-user-card-inner' style='display: flex; align-items: center; gap: 12px;'>
                                                                <span class='ts-user-card-icon' style='font-size: 22px;'>🚀</span>
                                                                <div>
                                                                    <div class='ts-user-card-title'>Medsos Admin Platform</div>
                                                                    <div class='ts-user-card-name'>{$adminName}</div>
                                                                </div>
                                                            </div>
                                                            <span class='ts-user-card-pill'>Publikator Live</span>
                                                        </div>");
                                                }

                                                return new HtmlString("
                                                    <div class='ts-user-card ts-card-dashed' style='display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px;'>
                                                        <span class='ts-user-card-icon' style='font-size: 22px; opacity: 0.5;'>🚀</span>
                                                        <div>
                                                            <div class='ts-user-card-title'>Medsos Admin Platform</div>
                                                            <div class='ts-user-card-name'>Belum dipublikasikan...</div>
                                                        </div>
                                                    </div>");
                                            })
                                            ->columnSpanFull(),

                                        DatePicker::make('tanggal_posting')
                                            ->label('Tanggal Publikasi')
                                            ->native(false)
                                            ->displayFormat('d F Y')
                                            ->default(now()->format('Y-m-d')),

                                        TextInput::make('link_postingan')
                                            ->url()
                                            ->label('Link Postingan')
                                            ->placeholder('https://instagram.com/p/...')
                                            ->columnSpanFull(),
                                    ])
                                    ->disabled(function ($record) {
                                        $user = Auth::user();
                                        if (! $user) {
                                            return true;
                                        }
                                        if ($user->isAdmin()) {
                                            return false;
                                        }
                                        if ($record && $record->status === 'selesai') {
                                            return true;
                                        }
                                        return ! $user->isMedsosAdminPlatform();
                                    }),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),

                        // =================================================================
                        // KOLOM KANAN (LEBAR: 1/3) - PANEL DISKUSI & KOMENTAR (STICKY)
                        // =================================================================
                        Group::make()
                            ->schema([
                                Section::make('💬 Diskusi & Komentar Tim')
                                    ->description('Ruang obrolan internal tim medsos terkait konten ini.')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->collapsible()
                                    ->schema([
                                        Livewire::make(ContentComments::class, fn($record) => [
                                            'contentId' => $record?->id,
                                        ])
                                            ->key(fn($record) => 'content-comments-' . ($record?->id ?? 'new')),
                                    ]),
                            ])
                            ->extraAttributes([
                                'class' => 'lg:sticky lg:top-6 self-start',
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
