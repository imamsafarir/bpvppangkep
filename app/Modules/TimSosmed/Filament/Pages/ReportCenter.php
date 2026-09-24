<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Models\User;
use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Models\Content;
use App\Modules\TimSosmed\Models\Platform;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Action as TableAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ReportCenter extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Pusat Laporan';
    protected static ?int $navigationSort = 10;

    protected string $view = 'timsosmed::filament.pages.report-center';
    protected ?string $heading = '📑 Pusat Analisis & Laporan Publikasi';
    protected ?string $subheading = 'Rekapitulasi, statistik performa, dan unduhan dokumen resmi produksi media sosial BPVP Pangkep.';

    public static function canAccess(): bool
    {
        return Auth::user()?->isMedsosTeam() ?? false;
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->calculateExecutiveStats(),
        ];
    }

    /**
     * Hitung ringkasan statistik performa untuk dashboard atas
     */
    protected function calculateExecutiveStats(): array
    {
        $now = now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        $totalSelesai = Content::query()->where('status', 'selesai')->count();

        $selesaiBulanIni = Content::query()
            ->where('status', 'selesai')
            ->whereBetween('tanggal_posting', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->count();

        $selesaiMingguIni = Content::query()
            ->where('status', 'selesai')
            ->whereBetween('tanggal_posting', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->count();

        $selesaiTahunIni = Content::query()
            ->where('status', 'selesai')
            ->whereBetween('tanggal_posting', [$startOfYear->toDateString(), $now->toDateString()])
            ->count();

        // Distribusi konten per platform resmi
        $platforms = Platform::withCount(['contents' => fn($q) => $q->where('status', 'selesai')])->get();
        $platformBreakdown = [];
        $topPlatform = null;
        $maxPlatformCount = 0;

        foreach ($platforms as $p) {
            $count = (int) $p->contents_count;
            $pct = $totalSelesai > 0 ? round(($count / $totalSelesai) * 100, 1) : 0;

            $brand = match (strtolower(trim($p->name))) {
                'instagram' => [
                    'icon' => '📸',
                    'bg' => 'linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045)',
                    'color' => '#db2777',
                    'bar' => '#db2777',
                ],
                'facebook' => [
                    'icon' => '📘',
                    'bg' => '#1877f2',
                    'color' => '#2563eb',
                    'bar' => '#2563eb',
                ],
                'tiktok' => [
                    'icon' => '🎵',
                    'bg' => '#000000',
                    'color' => '#1e293b',
                    'bar' => '#0f172a',
                ],
                'youtube' => [
                    'icon' => '▶️',
                    'bg' => '#ff0000',
                    'color' => '#dc2626',
                    'bar' => '#ef4444',
                ],
                default => [
                    'icon' => '🌐',
                    'bg' => '#6366f1',
                    'color' => '#4f46e5',
                    'bar' => '#6366f1',
                ],
            };

            $platformBreakdown[] = [
                'id' => $p->id,
                'name' => $p->name,
                'count' => $count,
                'percentage' => $pct,
                'brand' => $brand,
            ];
        }

        // Cari platform teraktif dan tangani jika ada lebih dari 1 platform dengan jumlah tertinggi yang sama (imbang)
        $counts = array_column($platformBreakdown, 'count');
        $maxPlatformCount = !empty($counts) ? max($counts) : 0;

        $topPlatforms = [];
        if ($maxPlatformCount > 0) {
            $topPlatforms = array_values(array_filter($platformBreakdown, fn($item) => $item['count'] === $maxPlatformCount));
        }

        $topPlatformNames = array_column($topPlatforms, 'name');
        $topPlatformText = match (count($topPlatformNames)) {
            0 => 'Belum ada',
            1 => $topPlatformNames[0],
            2 => $topPlatformNames[0] . ' & ' . $topPlatformNames[1],
            default => implode(', ', array_slice($topPlatformNames, 0, -1)) . ', & ' . end($topPlatformNames),
        };

        // Kontributor teraktif
        $topPlanner = Content::where('status', 'selesai')->whereNotNull('planner_id')
            ->selectRaw('planner_id, count(*) as total')
            ->groupBy('planner_id')
            ->orderByDesc('total')
            ->with('planner')
            ->first();

        $topEditor = Content::where('status', 'selesai')->whereNotNull('editor_id')
            ->selectRaw('editor_id, count(*) as total')
            ->groupBy('editor_id')
            ->orderByDesc('total')
            ->with('editor')
            ->first();

        $topAdmin = Content::where('status', 'selesai')->whereNotNull('admin_id')
            ->selectRaw('admin_id, count(*) as total')
            ->groupBy('admin_id')
            ->orderByDesc('total')
            ->with('admin')
            ->first();

        return [
            'totalSelesai' => $totalSelesai,
            'selesaiBulanIni' => $selesaiBulanIni,
            'selesaiMingguIni' => $selesaiMingguIni,
            'selesaiTahunIni' => $selesaiTahunIni,
            'topPlatform' => $topPlatformText,
            'topPlatforms' => $topPlatforms,
            'topPlatformCount' => $maxPlatformCount,
            'topPlatformIsTie' => count($topPlatforms) > 1,
            'platformBreakdown' => $platformBreakdown,
            'topPlanner' => $topPlanner?->planner?->name ?? '-',
            'topPlannerCount' => $topPlanner?->total ?? 0,
            'topEditor' => $topEditor?->editor?->name ?? '-',
            'topEditorCount' => $topEditor?->total ?? 0,
            'topAdmin' => $topAdmin?->admin?->name ?? '-',
            'topAdminCount' => $topAdmin?->total ?? 0,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // 1. UNDUH CEPAT BULAN INI
            Action::make('quickDownloadMonth')
                ->label('Laporan Bulan Ini')
                ->icon('heroicon-m-bolt')
                ->color('success')
                ->action(function () {
                    $dari = now()->startOfMonth()->toDateString();
                    $sampai = now()->toDateString();

                    $records = Content::where('status', 'selesai')
                        ->whereBetween('tanggal_posting', [$dari, $sampai])
                        ->with(['planner', 'editor', 'admin', 'instruktur', 'platforms'])
                        ->latest('tanggal_posting')
                        ->get();

                    if ($records->isEmpty()) {
                        Notification::make()
                            ->title('Belum Ada Konten Bulan Ini')
                            ->body('Tidak ada konten berstatus Selesai pada bulan ini (' . now()->translatedFormat('F Y') . ').')
                            ->warning()
                            ->send();
                        return;
                    }

                    return $this->generatePdfReport($records, 'LAPORAN BULANAN: ' . now()->translatedFormat('F Y'), [
                        'periode_label' => now()->translatedFormat('F Y'),
                        'dari_tanggal' => $dari,
                        'sampai_tanggal' => $sampai,
                    ]);
                }),

            // 2. UNDUH LAPORAN BERDASARKAN FILTER PERIODIK
            Action::make('downloadByRange')
                ->label('Download Laporan Lengkap')
                ->icon('heroicon-m-calendar-days')
                ->color('primary')
                ->modalHeading('Konfigurasi Cetak Laporan Resmi BPVP Pangkep')
                ->modalDescription('Tentukan rentang tanggal posting dan kriteria konten untuk dicetak sebagai dokumen PDF resmi.')
                ->modalSubmitActionLabel('Unduh PDF Resmi')
                ->form([
                    Grid::make(['default' => 1, 'md' => 2])
                        ->schema([
                            DatePicker::make('dari_tanggal')
                                ->label('Dari Tanggal Posting')
                                ->required()
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->default(now()->startOfMonth()->toDateString()),

                            DatePicker::make('sampai_tanggal')
                                ->label('Sampai Tanggal Posting')
                                ->required()
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->default(now()->toDateString()),
                        ]),

                    Select::make('platform_id')
                        ->label('Filter Platform Media Sosial')
                        ->placeholder('Semua Platform (Default)')
                        ->options(Platform::pluck('name', 'id'))
                        ->nullable()
                        ->searchable(),

                    Select::make('planner_id')
                        ->label('Filter Petugas Planner / Instruktur')
                        ->placeholder('Semua Planner (Default)')
                        ->options(User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'medsos_planner', 'instruktur']))->pluck('name', 'id'))
                        ->nullable()
                        ->searchable(),

                    Toggle::make('sertakan_tanda_tangan')
                        ->label('Sertakan Lembar Pengesahan (Tanda Tangan)')
                        ->default(true)
                        ->helperText('Menampilkan kolom tanda tangan Subkoordinator & Penanggung Jawab Tim Media Sosial.')
                        ->live(),

                    Grid::make(['default' => 1, 'md' => 2])
                        ->visible(fn($get) => (bool) $get('sertakan_tanda_tangan'))
                        ->schema([
                            TextInput::make('nama_pejabat')
                                ->label('Jabatan / Pejabat Mengetahui')
                                ->default('Subkoordinator Pemberdayaan Pelatihan')
                                ->required(),

                            TextInput::make('pejabat_nama')
                                ->label('Nama Lengkap Pejabat (Opsional)')
                                ->placeholder('Contoh: Nama Pejabat, S.T., M.M.'),

                            TextInput::make('nip_pejabat')
                                ->label('NIP Pejabat (Opsional)')
                                ->placeholder('Contoh: 1985xxxxxxxxxxxx'),
                        ]),
                ])
                ->action(function (array $data) {
                    $dari = $data['dari_tanggal'];
                    $sampai = $data['sampai_tanggal'];

                    $query = Content::where('status', 'selesai')
                        ->whereBetween('tanggal_posting', [$dari, $sampai])
                        ->with(['planner', 'editor', 'admin', 'instruktur', 'platforms'])
                        ->latest('tanggal_posting');

                    if (!empty($data['platform_id'])) {
                        $query->whereHas('platforms', fn($q) => $q->where('platforms.id', $data['platform_id']));
                    }

                    if (!empty($data['planner_id'])) {
                        $query->where('planner_id', $data['planner_id']);
                    }

                    $records = $query->get();

                    if ($records->isEmpty()) {
                        Notification::make()
                            ->title('Data Tidak Ditemukan')
                            ->body('Tidak ada konten selesai yang sesuai kriteria rentang tanggal dan filter.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $periodeLabel = Carbon::parse($dari)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampai)->translatedFormat('d F Y');

                    return $this->generatePdfReport($records, 'LAPORAN PERIODIK: ' . $periodeLabel, [
                        'periode_label' => $periodeLabel,
                        'dari_tanggal' => $dari,
                        'sampai_tanggal' => $sampai,
                        'sertakan_tanda_tangan' => $data['sertakan_tanda_tangan'] ?? true,
                        'nama_pejabat' => $data['nama_pejabat'] ?? 'Subkoordinator Pemberdayaan Pelatihan',
                        'pejabat_nama' => $data['pejabat_nama'] ?? null,
                        'nip_pejabat' => $data['nip_pejabat'] ?? null,
                    ]);
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Content::query()->where('status', 'selesai')->latest('tanggal_posting'))
            ->defaultSort('tanggal_posting', 'desc')
            ->columns([
                // 1. THUMBNAIL / MEDIA VISUAL
                TextColumn::make('thumbnail')
                    ->label('Media')
                    ->html()
                    ->state(function (Content $record) {
                        $media = $record->getFirstMedia('hasil_edit') ?? $record->getFirstMedia('mentah');
                        if ($media) {
                            $url = $media->getUrl();
                            return "<div style='width: 48px; height: 48px; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.08); background: #f8fafc; display: flex; align-items: center; justify-content: center;'>
                                <img src='{$url}' style='width: 100%; height: 100%; object-fit: cover;' alt='Media'>
                            </div>";
                        }

                        $icon = match (strtolower((string) $record->jenis_konten)) {
                            'video' => '🎬',
                            'reels' => '📱',
                            'story' => '⚡',
                            'carousel' => '📑',
                            default => '🖼️',
                        };

                        return "<div style='width: 48px; height: 48px; border-radius: 10px; background: #f1f5f9; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 20px;' title='Visual Konten'>{$icon}</div>";
                    })
                    ->alignCenter(),

                // 2. JUDUL KONTEN & TIPE
                TextColumn::make('nama_kegiatan')
                    ->label('Judul & Tema Konten')
                    ->searchable()
                    ->weight('bold')
                    ->description(function (Content $record) {
                        $tipe = $record->jenis_konten ? ucfirst($record->jenis_konten) : 'Publikasi';
                        return "Format: {$tipe}";
                    })
                    ->wrap(),

                // 3. SUSUNAN TIM BERTUGAS
                TextColumn::make('tim')
                    ->label('Tim Produksi')
                    ->html()
                    ->state(function ($record) {
                        $plannerNames = array_filter([
                            $record->instruktur?->name,
                            $record->planner?->name,
                        ]);
                        $plannerText = !empty($plannerNames) ? implode(' & ', $plannerNames) : '-';
                        $ed = $record->editor?->name ?? '-';
                        $adm = $record->admin?->name ?? '-';

                        return "
                            <div style='display: flex; flex-direction: column; gap: 3px; font-size: 11px; line-height: 1.25;'>
                                <div style='display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; border-radius: 6px; background: #fffbeb; color: #92400e; border: 1px solid #fde68a;'>
                                    <span style='font-size: 9.5px; font-weight: 800; text-transform: uppercase;'>PLAN:</span>
                                    <span style='font-weight: 600; white-space: nowrap;'>{$plannerText}</span>
                                </div>
                                <div style='display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; border-radius: 6px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;'>
                                    <span style='font-size: 9.5px; font-weight: 800; text-transform: uppercase;'>EDIT:</span>
                                    <span style='font-weight: 600; white-space: nowrap;'>{$ed}</span>
                                </div>
                                <div style='display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; border-radius: 6px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;'>
                                    <span style='font-size: 9.5px; font-weight: 800; text-transform: uppercase;'>PUB:</span>
                                    <span style='font-weight: 600; white-space: nowrap;'>{$adm}</span>
                                </div>
                            </div>
                        ";
                    })
                    ->searchable(['instruktur.name', 'planner.name', 'editor.name', 'admin.name']),

                // 4. TANGGAL KEGIATAN
                TextColumn::make('tanggal_kegiatan')
                    ->label('Tgl Kegiatan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 5. TANGGAL POSTING RESMI
                TextColumn::make('tanggal_posting')
                    ->label('Tgl Publikasi')
                    ->date('d M Y')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                // 6. PLATFORM
                TextColumn::make('platforms.name')
                    ->label('Platform')
                    ->badge()
                    ->colors([
                        'primary' => 'Facebook',
                        'danger' => 'Instagram',
                        'warning' => 'TikTok',
                        'info' => 'Twitter / X',
                        'success' => 'YouTube',
                    ]),

                // 7. LINK POSTINGAN (TAUTAN LIVE)
                TextColumn::make('link_postingan')
                    ->label('Link Postingan')
                    ->html()
                    ->state(function (Content $record) {
                        if (empty($record->link_postingan)) {
                            return "<span style='color: #94a3b8; font-size: 11px; font-style: italic;'>-</span>";
                        }

                        $url = e($record->link_postingan);
                        return "<a href='{$url}' target='_blank' rel='noopener noreferrer' style='display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 8px; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 11.5px; font-weight: 700; text-decoration: none;'>
                            <span>Buka Post</span>
                            <svg style='width: 12px; height: 12px;' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'/></svg>
                        </a>";
                    }),
            ])
            ->filters([
                // 1. FILTER RENTANG TANGGAL POSTING
                Filter::make('tanggal_posting')
                    ->label('Rentang Tanggal Publikasi')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d M Y'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn(Builder $q, $date) => $q->whereDate('tanggal_posting', '>=', $date))
                            ->when($data['sampai'], fn(Builder $q, $date) => $q->whereDate('tanggal_posting', '<=', $date));
                    }),

                // 2. FILTER PLATFORM
                SelectFilter::make('platforms')
                    ->label('Platform Medsos')
                    ->relationship('platforms', 'name')
                    ->preload(),

                // 3. FILTER PLANNER
                SelectFilter::make('planner_id')
                    ->label('Medsos Planner')
                    ->relationship('planner', 'name')
                    ->searchable()
                    ->preload(),

                // 4. FILTER EDITOR
                SelectFilter::make('editor_id')
                    ->label('Medsos Editor')
                    ->relationship('editor', 'name')
                    ->searchable()
                    ->preload(),

                // 5. FILTER ADMIN
                SelectFilter::make('admin_id')
                    ->label('Admin Platform')
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // 1. LIHAT KONTEN DETAIL
                TableAction::make('viewContent')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn(Content $record) => ContentResource::getUrl('view', ['record' => $record])),

                // 2. UNDUH BUKTI PUBLIKASI PDF PER BARIS
                TableAction::make('downloadRowPdf')
                    ->label('Bukti PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Content $record) {
                        return $this->generatePdfReport(
                            collect([$record]),
                            'BUKTI PUBLIKASI KONTEN: ' . $record->nama_kegiatan,
                            [
                                'periode_label' => $record->tanggal_posting ? Carbon::parse($record->tanggal_posting)->translatedFormat('d F Y') : 'Publikasi',
                                'sertakan_tanda_tangan' => true,
                                'nama_pejabat' => 'Subkoordinator Pemberdayaan Pelatihan',
                            ]
                        );
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('exportSelectedPdf')
                        ->label('Download PDF Terpilih')
                        ->icon('heroicon-m-document-text')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $records->loadMissing(['planner', 'editor', 'admin', 'instruktur', 'platforms']);

                            return $this->generatePdfReport(
                                $records,
                                'REKAPITULASI KONTEN PILIHAN BPVP PANGKEP',
                                [
                                    'periode_label' => 'Pilihan ' . count($records) . ' Konten Terpilih',
                                    'sertakan_tanda_tangan' => true,
                                    'nama_pejabat' => 'Subkoordinator Pemberdayaan Pelatihan',
                                ]
                            );
                        }),
                ]),
            ]);
    }

    /**
     * Helper umum untuk merender dan mengunduh berkas PDF
     */
    protected function generatePdfReport($records, string $title, array $options = [])
    {
        $collection = $records instanceof Collection ? $records : collect($records);

        $pdf = Pdf::loadView('timsosmed::pdf.rekap-pekerjaan', [
            'records' => $collection,
            'title' => $title,
            'periodeLabel' => $options['periode_label'] ?? '-',
            'sertakanTandaTangan' => $options['sertakan_tanda_tangan'] ?? true,
            'namaPejabat' => $options['nama_pejabat'] ?? 'Subkoordinator Pemberdayaan Pelatihan',
            'pejabatNama' => $options['pejabat_nama'] ?? null,
            'nipPejabat' => $options['nip_pejabat'] ?? null,
            'tanggalCetak' => now()->translatedFormat('d F Y, H:i') . ' WITA',
        ]);

        $pdf->setPaper('folio', 'portrait');

        $filename = 'Laporan_Publikasi_BPVP_Pangkep_' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
