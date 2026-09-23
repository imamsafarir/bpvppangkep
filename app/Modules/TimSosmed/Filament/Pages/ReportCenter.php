<?php

namespace App\Modules\TimSosmed\Filament\Pages;

use App\Modules\TimSosmed\Models\Content;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Action as TableAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ReportCenter extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\UnitEnum|null $navigationGroup = 'Tim Media Sosial';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Pusat Laporan';

    protected static ?int $navigationSort = 10;

    protected string $view = 'timsosmed::filament.pages.report-center';

    protected ?string $heading = 'Pusat Analisis & Laporan';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadByRange')
                ->label('Download Laporan Periodik')
                ->icon('heroicon-m-calendar-days')
                ->color('primary')
                ->modalHeading('Pilih Rentang Tanggal Laporan')
                ->modalDescription('Semua konten dengan status "Selesai" berdasarkan Tanggal Posting dalam rentang ini akan diunduh.')
                ->modalSubmitActionLabel('Unduh PDF')
                ->form([
                    DatePicker::make('dari_tanggal')->label('Dari Tanggal')->required()->default(now()->startOfMonth()),
                    DatePicker::make('sampai_tanggal')->label('Sampai Tanggal')->required()->default(now()),
                ])
                ->action(function (array $data) {
                    $dari = $data['dari_tanggal'];
                    $sampai = $data['sampai_tanggal'];

                    // --- UBAH LOGIKA: Gunakan tanggal_posting untuk pencarian dan urutan ---
                    $records = Content::where('status', 'selesai')
                        ->whereBetween('tanggal_posting', [$dari, $sampai])
                        ->with(['planner', 'platforms'])
                        ->latest('tanggal_posting')
                        ->get();

                    if ($records->isEmpty()) {
                        Notification::make()->title('Data Kosong')->body('Tidak ada konten selesai pada rentang tanggal tersebut.')->danger()->send();

                        return;
                    }
                    $pdf = Pdf::loadView('timsosmed::pdf.rekap-pekerjaan', ['records' => $records, 'title' => 'LAPORAN PERIODIK: ' . Carbon::parse($dari)->format('d/m/Y') . ' - ' . Carbon::parse($sampai)->format('d/m/Y')]);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, "Laporan_Periodik_{$dari}_ke_{$sampai}.pdf");
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            // --- UBAH LOGIKA: Tabel otomatis diurutkan berdasarkan tanggal_posting terbaru ---
            ->query(Content::query()->where('status', 'selesai')->latest('tanggal_posting'))
            ->columns([
                TextColumn::make('nama_kegiatan')
                    ->label('Judul Konten')
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('tim')
                    ->label('Tim Bertugas')
                    ->html()
                    ->state(function ($record) {
                        // 1. GABUNGKAN INSTRUKTUR & PLANNER (Bagian Khusus)
                        $plannerNames = array_filter([
                            $record->instruktur?->name,
                            $record->planner?->name,
                        ]);
                        $plannerText = !empty($plannerNames) ? implode(' & ', $plannerNames) : '-';

                        // 2. AMBIL EDITOR & ADMIN
                        $ed = $record->editor?->name ?? '-';
                        $adm = $record->admin?->name ?? '-';

                        // 3. RENDER TAMPILAN BERSUSUN
                        return "
            <div class='text-xs whitespace-nowrap space-y-0.5'>
                <div>
                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Planner</span>
                    <span class='text-gray-500 mr-1'>:</span>
                    <span class='font-semibold text-primary-600'>{$plannerText}</span>
                </div>
                <div>
                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Editor</span>
                    <span class='text-gray-500 mr-1'>:</span>
                    <span class='font-semibold text-success-600'>{$ed}</span>
                </div>
                <div>
                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Admin</span>
                    <span class='text-gray-500 mr-1'>:</span>
                    <span class='font-semibold text-info-600'>{$adm}</span>
                </div>
            </div>
        ";
                    })
                    ->searchable(['instruktur.name', 'planner.name', 'editor.name', 'admin.name'])
                    ->sortable(false),

                TextColumn::make('tanggal_kegiatan')
                    ->label('Tgl Kegiatan')
                    ->date('d M Y')
                    ->sortable(),

                // --- TAMBAHAN BARU: Kolom Tanggal Posting ---
                TextColumn::make('tanggal_posting')
                    ->label('Tgl Posting')
                    ->date('d M Y')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('platforms.name')
                    ->label('Platform')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('planner_id')
                    ->label('Cari Planner')
                    ->relationship('planner', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('editor_id')
                    ->label('Cari Editor')
                    ->relationship('editor', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('admin_id')
                    ->label('Cari Admin')
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                TableAction::make('downloadRowPdf')
                    ->label('PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Content $record) {
                        $pdf = Pdf::loadView('timsosmed::pdf.rekap-pekerjaan', [
                            'records' => [$record],
                            'title' => 'LAPORAN KONTEN: ' . $record->nama_kegiatan,
                        ]);

                        return response()->streamDownload(fn() => print($pdf->stream()), "Konten_{$record->id}.pdf");
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
                            $pdf = Pdf::loadView('timsosmed::pdf.rekap-pekerjaan', [
                                'records' => $records,
                                'title' => 'LAPORAN PILIHAN: ' . Auth::user()->name,
                            ]);

                            return response()->streamDownload(fn() => print($pdf->stream()), 'Rekap_Kustom.pdf');
                        }),
                ]),
            ]);
    }
}
