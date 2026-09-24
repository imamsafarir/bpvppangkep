<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Infolists;

use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Modules\TimSosmed\Livewire\ContentComments;
use Illuminate\Support\Str;
use ZipArchive;

class ContentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 1. INFORMASI UTAMA
                Section::make('Informasi Utama Konten')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('nama_kegiatan')->weight('bold')->size('lg'),
                            TextEntry::make('status')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'draft' => 'gray',
                                    'menunggu_editor' => 'warning',
                                    'revisi_editor', 'revisi_planner' => 'danger',
                                    'siap_publish' => 'info',
                                    'selesai' => 'success',
                                    default => 'gray',
                                }),
                            TextEntry::make('planner.name')->label('Dibuat Oleh (Planner)'),
                        ]),

                        Grid::make(2)->schema([
                            TextEntry::make('tanggal_kegiatan')->date('d F Y'),
                            TextEntry::make('platforms.name')->label('Platform Publish')->badge(),
                        ]),

                        TextEntry::make('brief')->markdown()->columnSpanFull(),
                        TextEntry::make('caption')
                            ->label('Caption Sosmed')
                            ->copyable()
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                // 2. SEKSI BAHAN (PLANNER)
                Section::make('Bahan Mentah & Referensi')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        TextEntry::make('link_media_mentah')
                            ->label('Link Folder Bahan Mentah')
                            ->icon('heroicon-m-link')
                            ->color('warning')
                            ->url(fn($record) => $record->link_media_mentah)
                            ->openUrlInNewTab()
                            ->placeholder('Belum ada link bahan mentah.')
                            ->visible(fn($record) => filled($record->link_media_mentah)),

                        TextEntry::make('link_referensi')
                            ->label('Link Referensi Ide')
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->url(fn($record) => $record->link_referensi)
                            ->openUrlInNewTab()
                            ->placeholder('Tidak ada link referensi.'),

                        Actions::make([
                            Action::make('download_mentah')
                                ->label('Download Bahan Mentah (.zip)')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('warning')
                                ->visible(fn($record) => $record->hasMedia('mentah'))
                                ->action(fn($record) => self::handleZipDownload($record, 'mentah')),
                        ]),

                        SpatieMediaLibraryImageEntry::make('media_mentah')
                            ->label('Preview Aset Lokal (Jika Ada)')
                            ->collection('mentah')
                            ->circular()
                            ->stacked()
                            ->visible(fn($record) => $record->hasMedia('mentah')),
                    ])->columns(2),

                // 3. SEKSI HASIL PRODUKSI (EDITOR & PUBLISH)
                Section::make('Hasil Produksi & Publikasi')
                    ->icon('heroicon-o-rocket-launch')
                    ->schema([
                        // --- TAMBAHAN PENANGGUNG JAWAB ---
                        Grid::make(2)->schema([
                            TextEntry::make('editor.name')
                                ->label('Editor (Produksi)')
                                ->placeholder('Belum dikerjakan editor')
                                ->icon('heroicon-m-paint-brush'),
                            TextEntry::make('admin.name')
                                ->label('Admin (Publikasi)')
                                ->placeholder('Belum dipublikasi')
                                ->icon('heroicon-m-globe-alt'),
                        ])->columnSpanFull(),

                        // --- LINK EXTERNAL (HASIL EDIT) ---
                        TextEntry::make('link_hasil_edit')
                            ->label('Link Video Hasil Final')
                            ->icon('heroicon-m-video-camera')
                            ->color('success')
                            ->url(fn($record) => $record->link_hasil_edit)
                            ->openUrlInNewTab()
                            ->placeholder('Hasil edit belum tersedia.')
                            ->visible(fn($record) => filled($record->link_hasil_edit)),

                        // --- LINK LIVE POSTINGAN ---
                        TextEntry::make('link_postingan')
                            ->label('Link Konten (Sudah Live)')
                            ->icon('heroicon-m-globe-alt')
                            ->color('info')
                            ->url(fn($record) => $record->link_postingan)
                            ->openUrlInNewTab()
                            ->placeholder('Konten belum dipublish.'),

                        Actions::make([
                            Action::make('download_hasil')
                                ->label('Download Hasil Final (.zip)')
                                ->icon('heroicon-o-check-badge')
                                ->color('success')
                                ->visible(fn($record) => $record->hasMedia('hasil_edit'))
                                ->action(fn($record) => self::handleZipDownload($record, 'hasil_edit')),
                        ]),

                        SpatieMediaLibraryImageEntry::make('hasil_edit')
                            ->label('Preview Hasil Lokal (Jika Ada)')
                            ->collection('hasil_edit')
                            ->circular()
                            ->stacked()
                            ->visible(fn($record) => $record->hasMedia('hasil_edit')),
                    ])->columns(2),

                // 4. LOG REVISI
                Section::make('Riwayat Perbaikan (Revisi)')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        RepeatableEntry::make('revisions')
                            ->label('')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Waktu')
                                    ->dateTime('d/m/Y H:i'),
                                TextEntry::make('target_revisi')
                                    ->label('Target')
                                    ->badge()
                                    ->color(fn($state) => $state === 'planner' ? 'warning' : 'danger'),
                                TextEntry::make('catatan')->label('Pesan Revisi'),
                            ])->columns(3),
                    ])->collapsible()->compact(),

                // 5. DISKUSI & KOMENTAR TIM
                Section::make('💬 Diskusi & Komentar Tim')
                    ->description('Ruang obrolan internal tim medsos terkait konten ini.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Livewire::make(ContentComments::class, fn($record) => [
                            'contentId' => $record?->id,
                        ])
                            ->key(fn($record) => 'infolist-comments-' . ($record?->id ?? 'new')),
                    ])->collapsible(),
            ]);
    }

    protected static function handleZipDownload($record, string $collection)
    {
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
