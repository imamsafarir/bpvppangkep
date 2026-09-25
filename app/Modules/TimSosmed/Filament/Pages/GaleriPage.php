<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Models\Content;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GaleriPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected string $view = 'timsosmed::filament.pages.galeri-page';

    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';

    protected static ?string $navigationLabel = 'Galeri Media';

    protected static ?int $navigationSort = 5;

    protected ?string $heading = '🖼️ Galeri Media Tim Sosmed';

    protected ?string $subheading = 'Semua bahan mentah dan hasil editing konten.';

    // Filter state
    public string $filterKoleksi = 'semua';
    public string $filterCari = '';
    public int $perPage = 24;
    public int $page = 1;

    protected $queryString = [
        'filterKoleksi' => ['except' => 'semua'],
        'filterCari' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && (
            $user->isAdmin() ||
            $user->isMedsosPlanner() ||
            $user->isMedsosEditor() ||
            $user->isMedsosAdminPlatform() ||
            $user->isInstruktur()
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('migrate_media')
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
        ];
    }

    /**
     * Ambil semua media dari koleksi bahan dan editing,
     * dengan filter dan pagination.
     */
    public function getMediaItems(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Media::query()
            ->whereIn('collection_name', $this->filterKoleksi === 'semua'
                ? ['bahan', 'editing']
                : [$this->filterKoleksi])
            ->where('model_type', Content::class)
            ->with('model')
            ->latest();

        if (filled($this->filterCari)) {
            $query->whereHas('model', function ($q) {
                $q->where('nama_kegiatan', 'like', '%' . $this->filterCari . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    public function updatedFilterKoleksi(): void
    {
        $this->page = 1;
    }

    public function updatedFilterCari(): void
    {
        $this->page = 1;
    }
}
