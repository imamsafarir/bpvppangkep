<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Modules\TimSosmed\Models\Content;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MyTasksTable extends BaseWidget
{
    protected static ?string $heading = '🎯 Tugas Prioritas Anda';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return Auth::user()?->isMedsosTeam() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = Auth::user();

                /** @var Builder $query */
                $query = Content::query()
                    ->with([
                        'platforms',
                        'pegawai',
                        'instruktur',
                        'planner',
                        'editor',
                        'admin',
                    ])
                    ->latest();

                if (! $user) {
                    return $query->whereRaw('1 = 0');
                }

                // Super Admin / Administrator melihat semua tugas yang belum selesai.
                if ($user->isAdmin()) {
                    return $query->where('status', '!=', 'selesai');
                }

                return $query
                    ->where('status', '!=', 'selesai')
                    ->where(function (Builder $taskQuery) use ($user) {
                        // Planner melihat semua konten yang masih di tahap perencanaan.
                        if ($user->isMedsosPlanner()) {
                            $taskQuery->orWhereIn('status', ['draft', 'revisi_planner'])
                                ->orWhere('planner_id', $user->id)
                                ->orWhere('pegawai_id', $user->id);
                        }

                        // Editor melihat semua konten yang menunggu editing / revisi editor.
                        if ($user->isMedsosEditor()) {
                            $taskQuery->orWhereIn('status', ['menunggu_editor', 'revisi_editor'])
                                ->orWhere('editor_id', $user->id);
                        }

                        // Admin Platform melihat semua konten siap publish.
                        if ($user->isMedsosAdminPlatform()) {
                            $taskQuery->orWhere('status', 'siap_publish')
                                ->orWhere('admin_id', $user->id);
                        }
                    });
            })

            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->label('KONTEN')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-document-text')
                    ->iconColor('primary')
                    ->description(
                        fn(Content $record) => 'Platform: ' . ($record->platforms->pluck('name')->join(', ') ?: '-')
                    ),

                Tables\Columns\TextColumn::make('jenis_konten')
                    ->label('JENIS')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'bahan' => 'warning',
                        'final' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'bahan' => 'Bahan',
                        'final' => 'Final',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('tim')
                    ->label('TIM')
                    ->html()
                    ->state(function (Content $record) {
                        $plannerText = collect([
                            $record->pegawai?->name,
                            $record->instruktur?->name,
                            $record->planner?->name,
                        ])
                            ->filter()
                            ->unique()
                            ->implode(' & ');

                        $editorText = $record->editor?->name ?? '-';
                        $adminText = $record->admin?->name ?? '-';

                        return "
                            <div class='text-xs whitespace-nowrap space-y-0.5'>
                                <div>
                                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Planner</span>
                                    <span class='text-gray-500 mr-1'>:</span>
                                    <span class='font-semibold text-primary-600'>" . e($plannerText ?: '-') . "</span>
                                </div>

                                <div>
                                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Editor</span>
                                    <span class='text-gray-500 mr-1'>:</span>
                                    <span class='font-semibold'>" . e($editorText) . "</span>
                                </div>

                                <div>
                                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Admin</span>
                                    <span class='text-gray-500 mr-1'>:</span>
                                    <span class='font-semibold'>" . e($adminText) . "</span>
                                </div>
                            </div>
                        ";
                    }),

                Tables\Columns\TextColumn::make('tanggal_kegiatan')
                    ->label('DEADLINE')
                    ->date('d M Y')
                    ->weight(FontWeight::Bold)
                    ->color(function (Content $record) {
                        $deadline = Carbon::parse($record->tanggal_kegiatan)->endOfDay();

                        if ($deadline->isPast()) {
                            return 'danger';
                        }

                        if ($deadline->isToday()) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->description(function (Content $record) {
                        $now = now();
                        $deadline = Carbon::parse($record->tanggal_kegiatan)->endOfDay();

                        if ($deadline->isPast()) {
                            $hariTelat = (int) $deadline->diffInDays($now);
                            $jamTelat = (int) ($deadline->diffInHours($now) % 24);

                            if ($hariTelat > 0) {
                                return "🚨 Telat {$hariTelat} hari, {$jamTelat} jam";
                            }

                            return "🚨 Telat {$jamTelat} jam";
                        }

                        if ($deadline->isToday()) {
                            $sisaJam = (int) $now->diffInHours($deadline);

                            return "🔥 Hari ini! sisa {$sisaJam} jam";
                        }

                        $sisaHari = (int) $now->diffInDays($deadline);

                        return "⏳ {$sisaHari} hari lagi";
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('PROSES')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'draft' => 'heroicon-m-pencil-square',
                        'menunggu_editor' => 'heroicon-m-clock',
                        'revisi_editor' => 'heroicon-m-exclamation-triangle',
                        'revisi_planner' => 'heroicon-m-arrow-path',
                        'siap_publish' => 'heroicon-m-eye',
                        'selesai' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'menunggu_editor' => 'warning',
                        'siap_publish' => 'info',
                        'selesai' => 'success',
                        'revisi_editor', 'revisi_planner' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'menunggu_editor' => 'Menunggu Editor',
                        'revisi_editor' => 'Revisi Editor',
                        'revisi_planner' => 'Revisi Planner',
                        'siap_publish' => 'Siap Publish',
                        'selesai' => 'Selesai',
                        default => ucfirst($state),
                    }),
            ])

            ->recordActions([
                Action::make('proses')
                    ->label('Kerjakan')
                    ->button()
                    ->size('sm')
                    ->icon('heroicon-m-play-circle')
                    ->color('primary')
                    ->tooltip('Klik untuk membuka detail konten')
                    ->url(fn(Content $record): string => ContentResource::getUrl('edit', ['record' => $record])),
            ])

            ->emptyStateHeading('Semua tugas selesai! 🎉')
            ->emptyStateDescription('Tidak ada konten yang butuh perhatian Anda saat ini.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->paginated(false)
            ->striped();
    }
}
