<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Pages;

use App\Modules\TimSosmed\Filament\Pages\CalendarPage;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Models\Content;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListContents extends ListRecords
{
    protected static string $resource = ContentResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('migrate_media')
                ->label('🔄 Sinkronisasi Folder Media')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn() => Auth::user()?->isAdmin() ?? false)
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Folder Media TimSosmed')
                ->modalDescription('Proses ini akan memastikan seluruh file bahan & hasil editing tersimpan rapi di folder timsosmed/content/ dan seluruh preview AVIF terbuat dengan baik. Lanjutkan?')
                ->modalSubmitActionLabel('Ya, Sinkronisasi Sekarang')
                ->action(function () {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('timsosmed:migrate-media');

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Sinkronisasi Media Selesai')
                            ->body('Seluruh media TimSosmed berhasil diselaraskan dan preview AVIF telah siap.')
                            ->send();
                    } catch (\Throwable $e) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Sinkronisasi Gagal')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Actions\Action::make('kalender')
                ->label('📅 Buka Kalender Konten')
                ->color('gray')
                ->url(fn() => CalendarPage::getUrl()),

            Actions\CreateAction::make()
                ->label('✨ Buat Konten Baru')
                ->visible(fn() => Auth::user()?->isAdmin() || Auth::user()?->isMedsosTeam() || Auth::user()?->isStaff()),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $baseQuery = fn() => Content::query()->when(
            $user && ! ($user->isAdmin() || $user->isMedsosTeam()),
            fn($q) => $q->where(fn($sub) => $sub->where('pegawai_id', $user->id)
                ->orWhere('instruktur_id', $user->id)
                ->orWhere('planner_id', $user->id))
        );

        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-m-squares-2x2')
                ->badge($baseQuery()->count()),

            'draft' => Tab::make('Draft / Konsep')
                ->icon('heroicon-m-pencil-square')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'draft'))
                ->badge($baseQuery()->where('status', 'draft')->count())
                ->badgeColor('gray'),

            'proses_edit' => Tab::make('Proses Editing')
                ->icon('heroicon-m-paint-brush')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['menunggu_editor', 'revisi_editor']))
                ->badge($baseQuery()->whereIn('status', ['menunggu_editor', 'revisi_editor'])->count())
                ->badgeColor('warning'),

            'siap_publish' => Tab::make('Siap Publish')
                ->icon('heroicon-m-rocket-launch')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'siap_publish'))
                ->badge($baseQuery()->where('status', 'siap_publish')->count())
                ->badgeColor('info'),

            'selesai' => Tab::make('Selesai / Live')
                ->icon('heroicon-m-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'selesai'))
                ->badge($baseQuery()->where('status', 'selesai')->count())
                ->badgeColor('success'),

            'revisi' => Tab::make('Perlu Revisi')
                ->icon('heroicon-m-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['revisi_planner', 'revisi_editor']))
                ->badge($baseQuery()->whereIn('status', ['revisi_planner', 'revisi_editor'])->count())
                ->badgeColor('danger'),
        ];
    }
}
