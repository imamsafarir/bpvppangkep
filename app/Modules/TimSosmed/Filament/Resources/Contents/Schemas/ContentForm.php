<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Schemas;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions as ActionsContainer;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use ZipArchive;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        $internalRoles = [
            'super_admin',
            'admin_platform',
            'planner',
            'editor',
            'instruktur',
            'pegawai',
        ];

        $sendToEditorRoles = [
            'super_admin',
            'planner',
        ];

        $creatorAsPegawaiRoles = [
            'pegawai',
            'editor',
            'admin_platform',
        ];

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
                // 2. SECTION STATUS ALUR KERJA (PASTI KUNING & PASTI DI TENGAH)
                // =========================================================================
                Section::make()
                    ->schema([
                        ToggleButtons::make('status')
                            ->label(false)
                            ->disabled(fn() => ! Auth::user()?->hasRole('super_admin'))
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
                        // KOLOM KIRI (LEBAR: 2/3) - 3 SECTION ALUR PEKERJAAN
                        // =================================================================
                        Group::make()
                            ->schema([

                                // -----------------------------------------------------
                                // SECTION 1: PERENCANAAN
                                // -----------------------------------------------------
                                Section::make('1. Perencanaan (Seluruh Pegawai)')
                                    ->description('Informasi dasar kegiatan dan aset mentah.')
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
                                                    && Auth::user()?->hasAnyRole($sendToEditorRoles)
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                ! Auth::user()?->hasAnyRole($sendToEditorRoles)
                                                    || ($record && $record->status === 'selesai' && ! Auth::user()?->hasRole('super_admin'))
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'menunggu_editor',
                                                    'planner_id' => Auth::id(),
                                                ]);

                                                $editors = User::role('editor')->get();

                                                Notification::make()
                                                    ->title('📩 Konten Menunggu Edit')
                                                    ->body("Planner mengirim brief baru: '{$record->nama_kegiatan}'.")
                                                    ->warning()
                                                    ->actions([
                                                        Action::make('view')
                                                            ->label('Buka Konten')
                                                            ->url(ContentResource::getUrl('edit', ['record' => $record]))
                                                            ->button()
                                                            ->markAsRead()
                                                            ->close(),
                                                    ])
                                                    ->sendToDatabase($editors);

                                                Notification::make()
                                                    ->title('Berhasil kirim ke Editor')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => ContentResource::getUrl('index')),

                                        Action::make('kirim_admin_langsung')
                                            ->label('Kirim ke Admin Langsung')
                                            ->icon('heroicon-m-shield-check')
                                            ->color('success')
                                            ->button()
                                            ->requiresConfirmation()
                                            ->visible(
                                                fn($record) => $record
                                                    && $record->status === 'revisi_planner'
                                                    && Auth::user()?->hasAnyRole($sendToEditorRoles)
                                            )
                                            ->disabled(
                                                fn($record) =>
                                                ! Auth::user()?->hasAnyRole($sendToEditorRoles)
                                                    || ($record && $record->status === 'selesai' && ! Auth::user()?->hasRole('super_admin'))
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'siap_publish',
                                                    'planner_id' => Auth::id(),
                                                ]);

                                                $admins = User::role('admin_platform')->get();

                                                Notification::make()
                                                    ->title('🚀 Konten Siap Publish')
                                                    ->body("Planner mengirim revisi langsung ke tahap akhir: '{$record->nama_kegiatan}'.")
                                                    ->warning()
                                                    ->actions([
                                                        Action::make('view')
                                                            ->label('Buka Konten')
                                                            ->url(ContentResource::getUrl('edit', ['record' => $record]))
                                                            ->button()
                                                            ->markAsRead()
                                                            ->close(),
                                                    ])
                                                    ->sendToDatabase($admins);

                                                Notification::make()
                                                    ->title('Berhasil dikirim langsung ke Admin')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => ContentResource::getUrl('index')),
                                    ])
                                    ->schema([
                                        Placeholder::make('info_penanggung_jawab')
                                            ->label('Tim Bertugas')
                                            ->content(function ($record) {
                                                if (! $record) {
                                                    $roleLabel = match (true) {
                                                        Auth::user()?->hasRole('instruktur') => 'Instruktur',
                                                        Auth::user()?->hasRole('planner') => 'Planner',
                                                        Auth::user()?->hasRole('pegawai') => 'Pegawai',
                                                        Auth::user()?->hasRole('editor') => 'Editor',
                                                        Auth::user()?->hasRole('admin_platform') => 'Admin Platform',
                                                        Auth::user()?->hasRole('super_admin') => 'Super Admin',
                                                        default => 'Pegawai',
                                                    };

                                                    return new HtmlString("
                                                        <span class='text-gray-500'>Dibuat oleh:</span>
                                                        <strong class='text-primary-600'>" . Auth::user()->name . "</strong>
                                                        <span class='italic'>( {$roleLabel} )</span>
                                                    ");
                                                }

                                                $record = $record->fresh([
                                                    'pegawai',
                                                    'instruktur',
                                                    'planner',
                                                ]);

                                                $plannerDisplay = collect([
                                                    $record->pegawai?->name,
                                                    $record->instruktur?->name,
                                                    $record->planner?->name,
                                                ])
                                                    ->filter()
                                                    ->unique()
                                                    ->implode(' & ');

                                                if (! $plannerDisplay) {
                                                    return new HtmlString("
                                                        <span class='text-gray-500'>Belum ada tim bertugas.</span>
                                                    ");
                                                }

                                                return new HtmlString("
                                                    <div class='flex flex-col gap-1'>
                                                        <div>
                                                            <span class='text-gray-500'>Planner:</span>
                                                            <strong class='text-primary-600'>{$plannerDisplay}</strong>
                                                        </div>
                                                    </div>
                                                ");
                                            })
                                            ->extraAttributes([
                                                'class' => 'bg-white p-4 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10',
                                            ])
                                            ->hintIcon('heroicon-m-user-circle')
                                            ->columnSpanFull(),

                                        Grid::make(['default' => 1, 'md' => 2])
                                            ->schema([
                                                Hidden::make('pegawai_id')
                                                    ->default(fn() => Auth::user()?->hasAnyRole($creatorAsPegawaiRoles) ? Auth::id() : null)
                                                    ->dehydrated(fn($state, $record) => blank($record?->pegawai_id) && filled($state)),

                                                Hidden::make('instruktur_id')
                                                    ->default(fn() => Auth::user()?->hasRole('instruktur') ? Auth::id() : null)
                                                    ->dehydrated(fn($state, $record) => blank($record?->instruktur_id) && filled($state)),

                                                Hidden::make('planner_id')
                                                    ->default(fn() => Auth::user()?->hasAnyRole(['planner', 'super_admin']) ? Auth::id() : null)
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
                                                            ->visible(fn() => Auth::user()?->hasAnyRole($internalRoles))
                                                            ->action(fn(Set $set) => $set('tanggal_kegiatan', now()->format('Y-m-d'))),

                                                        Action::make('tomorrow')
                                                            ->label('Besok')
                                                            ->visible(fn() => Auth::user()?->hasAnyRole($internalRoles))
                                                            ->action(fn(Set $set) => $set('tanggal_kegiatan', now()->addDay()->format('Y-m-d'))),

                                                        Action::make('nextWeek')
                                                            ->label('1 Minggu')
                                                            ->visible(fn() => Auth::user()?->hasAnyRole($internalRoles))
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

                                                        ActionsContainer::make([
                                                            Action::make('download_mentah')
                                                                ->label('Download Bahan Mentah')
                                                                ->icon('heroicon-o-arrow-down-tray')
                                                                ->color('warning')
                                                                ->visible(fn($record) => $record !== null && $record->hasMedia('mentah'))
                                                                ->action(fn($record) => self::handleZipDownload($record, 'mentah')),
                                                        ])
                                                            ->columnSpanFull(),

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

                                                        FileUpload::make('media_mentah')
                                                            ->label('Video Mentah (Direct Upload)')
                                                            ->hint('Fitur Mendatang 🚀')
                                                            ->hintColor('warning')
                                                            ->disabled()
                                                            ->placeholder('Fitur upload langsung akan tersedia pada update versi berikutnya')
                                                            ->extraAttributes([
                                                                'style' => 'filter: blur(2px); opacity: 0.6; cursor: not-allowed; pointer-events: none;',
                                                            ])
                                                            ->multiple()
                                                            ->reorderable()
                                                            ->appendFiles()
                                                            ->panelLayout('grid')
                                                            ->imagePreviewHeight('250')
                                                            ->disk('public')
                                                            ->directory('videos/mentah')
                                                            ->visibility('public')
                                                            ->preserveFilenames()
                                                            ->maxSize(512000)
                                                            ->extraAttributes([
                                                                'data-filepond-config' => json_encode([
                                                                    'chunkUploads' => true,
                                                                    'chunkSize' => 5000000,
                                                                    'chunkForce' => true,
                                                                ]),
                                                            ], merge: true)
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columnSpanFull()
                                                    ->columns(['default' => 1, 'md' => 2])
                                                    ->visible(fn(Get $get) => filled($get('jenis_konten'))),
                                            ]),
                                    ])
                                    ->disabled(fn() => ! Auth::user()?->hasAnyRole($internalRoles)),

                                // -----------------------------------------------------
                                // SECTION 2: HASIL EDITING
                                // -----------------------------------------------------
                                Section::make('2. Hasil Editing (Editor)')
                                    ->description('Proses editor & aset final.')
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
                                                    && Auth::user()?->hasRole(['super_admin', 'editor'])
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'siap_publish',
                                                    'editor_id' => Auth::id(),
                                                ]);

                                                $admins = User::role('admin_platform')->get();

                                                Notification::make()
                                                    ->title('👀 Konten Siap Review')
                                                    ->body("Editor telah menyelesaikan tugas '{$record->nama_kegiatan}'.")
                                                    ->info()
                                                    ->actions([
                                                        Action::make('view')
                                                            ->label('Review Konten')
                                                            ->url(ContentResource::getUrl('edit', ['record' => $record]))
                                                            ->button()
                                                            ->markAsRead()
                                                            ->close(),
                                                    ])
                                                    ->sendToDatabase($admins);

                                                Notification::make()
                                                    ->title('Berhasil submit ke Admin')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => ContentResource::getUrl('index')),
                                    ])
                                    ->schema([
                                        Placeholder::make('info_editor')
                                            ->label('Personel Editor')
                                            ->content(fn($record) => $record?->editor?->name ?? 'Menunggu proses editing...')
                                            ->extraAttributes(['class' => 'text-success-600 font-bold'])
                                            ->hintIcon('heroicon-m-paint-brush')
                                            ->columnSpanFull(),

                                        ActionsContainer::make([
                                            Action::make('download_hasil')
                                                ->label('Download Hasil Final')
                                                ->icon('heroicon-o-check-badge')
                                                ->color('success')
                                                ->visible(fn($record) => $record !== null && $record->hasMedia('hasil_edit'))
                                                ->action(fn($record) => self::handleZipDownload($record, 'hasil_edit')),
                                        ])
                                            ->columnSpanFull(),

                                        TextInput::make('link_hasil_edit')
                                            ->label('Link Google Drive / Nextcloud (Hasil Final)')
                                            ->helperText('Editor masukkan link hasil video yang sudah diedit di sini.')
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

                                        SpatieMediaLibraryFileUpload::make('hasil_edit')
                                            ->label('Hasil Editing (Direct Upload)')
                                            ->collection('hasil_edit')
                                            ->multiple()
                                            ->reorderable()
                                            ->panelLayout('grid')
                                            ->columnSpanFull()
                                            ->disabled()
                                            ->hint('Fitur Mendatang 🚀')
                                            ->hintColor('warning')
                                            ->placeholder('Fitur upload hasil video langsung akan segera hadir.')
                                            ->extraAttributes([
                                                'style' => 'filter: blur(2px); opacity: 0.6; cursor: not-allowed; pointer-events: none;',
                                            ]),
                                    ])
                                    ->disabled(
                                        fn($record) =>
                                        ! Auth::user()?->hasRole(['super_admin', 'editor'])
                                            || ($record && $record->status === 'selesai' && ! Auth::user()?->hasRole('super_admin'))
                                    ),

                                // -----------------------------------------------------
                                // SECTION 3: PUBLIKASI
                                // -----------------------------------------------------
                                Section::make('3. Publikasi (Admin Platform)')
                                    ->description('Tahap akhir & link postingan publikasi.')
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
                                                    && Auth::user()?->hasRole(['super_admin', 'admin_platform'])
                                            )
                                            ->action(function ($record, $livewire) {
                                                $livewire->save();

                                                $record->update([
                                                    'status' => 'selesai',
                                                    'admin_id' => Auth::id(),
                                                    'tanggal_posting' => now()->format('Y-m-d'),
                                                ]);

                                                $recipients = User::role([
                                                    'super_admin',
                                                    'admin_platform',
                                                    'planner',
                                                    'editor',
                                                ])->get();

                                                Notification::make()
                                                    ->title('🚀 KONTEN TELAH LIVE!')
                                                    ->body("Konten '{$record->nama_kegiatan}' sudah dipublikasi dan live.")
                                                    ->success()
                                                    ->icon('heroicon-o-check-badge')
                                                    ->actions([
                                                        Action::make('view')
                                                            ->label('Lihat Konten')
                                                            ->url(ContentResource::getUrl('view', ['record' => $record]))
                                                            ->button()
                                                            ->markAsRead()
                                                            ->close(),
                                                    ])
                                                    ->sendToDatabase($recipients)
                                                    ->send();

                                                Notification::make()
                                                    ->title('Konten Selesai & Notifikasi Terkirim')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->successRedirectUrl(fn() => ContentResource::getUrl('index')),
                                    ])
                                    ->schema([
                                        Placeholder::make('info_admin')
                                            ->label('Admin Publikasi')
                                            ->content(fn($record) => $record?->admin?->name ?? 'Belum dipublikasi')
                                            ->extraAttributes(['class' => 'text-info-600 font-bold'])
                                            ->hintIcon('heroicon-m-globe-alt')
                                            ->columnSpanFull(),

                                        TextInput::make('link_postingan')
                                            ->url()
                                            ->label('Link Postingan')
                                            ->columnSpanFull(),
                                    ])
                                    ->disabled(
                                        fn($record) =>
                                        ! Auth::user()?->hasRole(['super_admin', 'admin_platform'])
                                            || ($record && $record->status === 'selesai' && ! Auth::user()?->hasRole('super_admin'))
                                    ),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),

                        // =================================================================
                        // KOLOM KANAN (LEBAR: 1/3) - PANEL DISKUSI & KOMENTAR (STICKY / LOCKED)
                        // =================================================================
                        Group::make()
                            ->schema([
                                Section::make('💬 Diskusi & Komentar Tim')
                                    ->description('Ruang obrolan internal terkait postingan ini.')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->collapsible()
                                    ->schema([
                                        Placeholder::make('ruang_diskusi_placeholder')
                                            ->label(false)
                                            ->content(new HtmlString("
                                                <div class='flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl bg-gray-50/70 dark:bg-gray-800/50 space-y-3'>
                                                    <div class='text-4xl opacity-75'>🔒</div>

                                                    <div>
                                                        <span class='inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 border border-amber-200 dark:border-amber-700/50 shadow-sm'>
                                                            <span>🚀</span> Fitur Mendatang
                                                        </span>
                                                    </div>

                                                    <div class='font-bold text-gray-800 dark:text-gray-200 text-sm'>
                                                        Ruang Diskusi Tim (Terkunci)
                                                    </div>

                                                    <p class='text-xs text-gray-500 dark:text-gray-400 max-w-xs leading-relaxed'>
                                                        Fitur obrolan real-time antar Planner, Editor, dan Admin sedang dipersiapkan dan akan segera aktif.
                                                    </p>

                                                    <div class='w-full pt-2'>
                                                        <button type='button' disabled class='w-full py-2 px-3 text-xs font-semibold rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed border border-gray-300 dark:border-gray-600 shadow-none'>
                                                            Kirim Pesan (Segera Hadir)
                                                        </button>
                                                    </div>
                                                </div>
                                            ")),
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

    protected static function handleZipDownload($record, string $collection)
    {
        if (! $record) {
            return;
        }

        $media = $record->getMedia($collection);

        if ($media->isEmpty()) {
            return;
        }

        $folderName = Str::slug($record->nama_kegiatan);
        $zipName = "{$collection}_{$folderName}.zip";
        $zipPath = storage_path("app/public/{$zipName}");

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($media as $item) {
                if (file_exists($item->getPath())) {
                    $zip->addFile($item->getPath(), $item->file_name);
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
