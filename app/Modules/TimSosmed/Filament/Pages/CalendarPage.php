<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Filament\Widgets\BebanKerjaOverview;
use App\Modules\TimSosmed\Models\Content;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class CalendarPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected string $view = 'timsosmed::filament.pages.calendar-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Tim Media Sosial';

    protected static ?string $navigationLabel = 'Kalender Konten';

    protected static ?int $navigationSort = 4;

    protected ?string $heading = 'Kalender Jadwal Konten';

    /**
     * Memanggil widget agar muncul di bagian atas halaman (Header)
     */
    protected function getHeaderWidgets(): array
    {
        return [
            BebanKerjaOverview::class,
        ];
    }

    /**
     * Mengirim data event ke tampilan Blade
     */
    protected function getViewData(): array
    {
        // Tambahkan with() agar database tidak query berulang-ulang (N+1 problem)
        $events = Content::with(['planner', 'editor', 'admin'])->get()->map(function ($content) {
            return [
                'id' => $content->id,
                'title' => $content->nama_kegiatan,
                'start' => Carbon::parse($content->tanggal_kegiatan)->format('Y-m-d'),
                'url' => route('filament.admin.resources.contents.edit', ['record' => $content->id]),
                'color' => match ($content->status) {
                    'selesai' => '#22c55e',
                    'siap_publish' => '#3b82f6',
                    'draft' => '#6b7280',
                    default => '#ef4444',
                },
                // INI KUNCINYA: Bawa data tim ke kalender
                'extendedProps' => [
                    'planner' => $content->planner ? $content->planner->name : '-',
                    'editor' => $content->editor ? $content->editor->name : '-',
                    'admin' => $content->admin ? $content->admin->name : '-',
                ]
            ];
        })->toArray();

        return [
            'events' => json_encode($events),
        ];
    }
}
