<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Modules\TimSosmed\Models\Content;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContentChart extends ChartWidget
{
    // Properti standar Filament (Tanpa kata static)
    protected ?string $heading = '📈 Tren Produksi Konten (Bulan Ini)';

    protected ?string $pollingInterval = '15s';

    protected static ?int $sort = 3;

    /**
     * Properti publik untuk menangkap parameter dari Blade
     */
    public ?string $chartHeight = '300px'; // Tinggi normal yang proporsional untuk setengah layar (Dashboard)

    public bool $showHeader = true;

    /**
     * Menangkap data saat widget dimuat
     */
    public function mount(?string $height = null, bool $showHeader = true): void
    {
        if ($height) {
            $this->chartHeight = $height;
        }
        $this->showHeader = $showHeader;
    }

    /**
     * FIX ERROR: Menggunakan $this->heading (bukan static::)
     */
    public function getHeading(): ?string
    {
        return $this->showHeader ? $this->heading : null;
    }

    /**
     * Mengatur tinggi maksimal ke engine Chart.js
     */
    protected function getMaxHeight(): ?string
    {
        // Trik CSS: Karena Filament hanya mencetak `max-height` di blade view-nya,
        // kita sisipkan juga properti `height` agar grafiknya benar-benar dipaksa meluas sesuai ukuran.
        return $this->chartHeight . '; height: ' . $this->chartHeight;
    }

    protected function getData(): array
    {
        $dataSelesai = [];
        $labels = [];
        $today = now();

        for ($i = 1; $i <= $today->day; $i++) {
            $date = Carbon::create($today->year, $today->month, $i);
            $labels[] = $date->format('d');
            $dataSelesai[] = Content::query()
                ->whereDate('tanggal_posting', $date->toDateString())
                ->where('status', 'selesai')
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Konten Selesai',
                    'data' => $dataSelesai,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => '#10b981',
                    'borderWidth' => 3, // Membuat garis grafik tidak terlalu tipis
                    'tension' => 0.3,
                    'pointRadius' => 3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0, 'font' => ['size' => 10]],
                    'grid' => ['color' => 'rgba(200, 200, 200, 0.05)'],
                ],
                'x' => [
                    'display' => true,
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['size' => 10], 'autoSkip' => true],
                ],
            ],
            'plugins' => ['legend' => ['display' => false]],
            'layout' => [
                'padding' => ['left' => 10, 'right' => 15, 'top' => 10, 'bottom' => 0],
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadLastMonthReport')
                ->label('Laporan Bulan Lalu')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('info')
                ->size('sm')
                ->action(function () {
                    $lastMonth = now()->subMonth();
                    $records = Content::where('status', 'selesai')
                        ->whereBetween('tanggal_posting', [
                            $lastMonth->copy()->startOfMonth()->toDateString(),
                            $lastMonth->copy()->endOfMonth()->toDateString(),
                        ])
                        ->with(['planner', 'platforms'])->get();

                    $pdf = Pdf::loadView('timsosmed::pdf.rekap-pekerjaan', ['records' => $records, 'title' => 'Laporan Bulanan']);

                    return response()->streamDownload(fn() => print($pdf->stream()), 'Laporan.pdf');
                }),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
