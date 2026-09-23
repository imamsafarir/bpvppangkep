<?php

namespace App\Modules\TimSosmed\Filament\Resources\Contents\Tables;

use App\Modules\TimSosmed\Filament\Resources\Contents\ContentResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with([
                    'pegawai',
                    'instruktur',
                    'planner',
                    'editor',
                    'admin',
                    'platforms',
                ]);

                $user = Auth::user();

                if (! $user) {
                    return $query->whereRaw('1 = 0');
                }

                // Super Admin melihat semua konten
                if ($user->hasRole('super_admin')) {
                    return $query;
                }

                // Pegawai hanya melihat konten yang dia buat sendiri
                if ($user->hasRole('pegawai')) {
                    return $query->where('pegawai_id', $user->id);
                }

                // Instruktur hanya melihat konten yang dia buat sendiri
                if ($user->hasRole('instruktur')) {
                    return $query->where('instruktur_id', $user->id);
                }

                // Planner, Editor, Admin Platform tetap melihat semua konten
                return $query;
            })

            ->defaultSort('created_at', 'desc')

            ->recordUrl(function ($record) {
                if (Auth::user()->hasRole('super_admin')) {
                    return ContentResource::getUrl('edit', ['record' => $record]);
                }

                if ($record->status === 'selesai') {
                    return ContentResource::getUrl('view', ['record' => $record]);
                }

                return ContentResource::getUrl('edit', ['record' => $record]);
            })

            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->state(static function ($livewire, $rowLoop): int {
                        return (int) (
                            $livewire->getTableRecords()->total() -
                            (($livewire->getTableRecords()->currentPage() - 1) * $livewire->getTableRecords()->perPage()) -
                            $rowLoop->index
                        );
                    }),

                TextColumn::make('nama_kegiatan')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->platforms->pluck('name')->join(', ')),

                TextColumn::make('tim')
                    ->label('Tim Bertugas')
                    ->html()
                    ->state(function ($record) {
                        $plannerText = collect([
                            $record->pegawai?->name,
                            $record->instruktur?->name,
                            $record->planner?->name,
                        ])
                            ->filter()
                            ->unique()
                            ->implode(' & ');

                        $plannerText = $plannerText ?: '-';

                        $editorText = $record->editor?->name ?? '-';
                        $adminText = $record->admin?->name ?? '-';

                        return "
                            <div class='text-xs whitespace-nowrap space-y-0.5'>
                                <div>
                                    <span class='inline-block w-14 text-gray-500 font-medium opacity-75'>Planner</span>
                                    <span class='text-gray-500 mr-1'>:</span>
                                    <span class='font-semibold text-primary-600'>" . e($plannerText) . "</span>
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
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search) {
                            $query
                                ->whereHas('pegawai', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('instruktur', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('planner', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('editor', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('admin', fn(Builder $q) => $q->where('name', 'like', "%{$search}%"));
                        });
                    })
                    ->sortable(false),

                TextColumn::make('tanggal_kegiatan')
                    ->label('Tanggal Kegiatan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'menunggu_editor' => 'warning',
                        'revisi_editor', 'revisi_planner' => 'danger',
                        'siap_publish' => 'info',
                        'selesai' => 'success',
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

                TextColumn::make('tanggal_posting')
                    ->label('Tanggal Posting')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('link_media_mentah')
                    ->label('Bahan')
                    ->badge()
                    ->color(fn($state) => $state ? 'warning' : 'gray')
                    ->icon(fn($state) => $state ? 'heroicon-m-folder-open' : 'heroicon-m-x-circle')
                    ->formatStateUsing(fn($state) => $state ? 'Buka Bahan' : 'Kosong')
                    ->url(fn($record) => $record?->link_media_mentah)
                    ->openUrlInNewTab(),

                TextColumn::make('link_hasil_edit')
                    ->label('Hasil')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->icon(fn($state) => $state ? 'heroicon-m-video-camera' : 'heroicon-m-x-circle')
                    ->formatStateUsing(fn($state) => $state ? 'Buka Hasil' : 'Kosong')
                    ->url(fn($record) => $record?->link_hasil_edit)
                    ->openUrlInNewTab(),
            ])

            ->recordActions([
                ActionGroup::make([
                    Action::make('kirim_editor')
                        ->label('Kirim ke Editor')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) =>
                            Auth::user()->hasAnyRole(['super_admin', 'planner'])
                                && in_array($record->status, ['draft', 'revisi_planner'])
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'menunggu_editor',
                                'planner_id' => Auth::id(),
                            ]);

                            $recipients = User::role(['editor', 'super_admin'])->get();

                            Notification::make()
                                ->title('📩 Konten Menunggu Edit')
                                ->body("Planner telah mengirim brief: '{$record->nama_kegiatan}'.")
                                ->warning()
                                ->actions([
                                    NotificationAction::make('view')
                                        ->label('Buka Konten')
                                        ->url(ContentResource::getUrl('edit', ['record' => $record]))
                                        ->button()
                                        ->markAsRead()
                                        ->close(),
                                ])
                                ->sendToDatabase($recipients);

                            Notification::make()
                                ->title('Terkirim ke Editor')
                                ->success()
                                ->send();
                        }),

                    Action::make('serahkan_admin')
                        ->label('Submit ke Admin')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) =>
                            Auth::user()->hasAnyRole(['super_admin', 'editor'])
                                && in_array($record->status, ['menunggu_editor', 'revisi_editor'])
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'siap_publish',
                                'editor_id' => Auth::id(),
                            ]);

                            $recipients = User::role(['admin_platform', 'super_admin'])->get();

                            Notification::make()
                                ->title('👀 Konten Siap Review')
                                ->body("Editor telah menyelesaikan tugas: '{$record->nama_kegiatan}'.")
                                ->info()
                                ->actions([
                                    NotificationAction::make('view')
                                        ->label('Review Hasil')
                                        ->url(ContentResource::getUrl('edit', ['record' => $record]))
                                        ->button()
                                        ->markAsRead()
                                        ->close(),
                                ])
                                ->sendToDatabase($recipients);

                            Notification::make()
                                ->title('Berhasil submit ke Admin')
                                ->success()
                                ->send();
                        }),

                    Action::make('revisi')
                        ->label('Minta Revisi')
                        ->icon('heroicon-o-exclamation-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) =>
                            Auth::user()->hasAnyRole(['super_admin', 'admin_platform'])
                                && $record->status === 'siap_publish'
                        )
                        ->form([
                            Select::make('target_revisi')
                                ->label('Target Revisi')
                                ->options([
                                    'planner' => 'Planner / Pembuat Konten',
                                    'editor' => 'Editor',
                                ])
                                ->required(),

                            Textarea::make('catatan')
                                ->label('Catatan Revisi')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            $record->revisions()->create([
                                'user_id' => Auth::id(),
                                'catatan' => $data['catatan'],
                                'target_revisi' => $data['target_revisi'],
                            ]);

                            $status = $data['target_revisi'] === 'planner'
                                ? 'revisi_planner'
                                : 'revisi_editor';

                            $record->update([
                                'status' => $status,
                            ]);

                            $recipients = collect();

                            if ($data['target_revisi'] === 'planner') {
                                if ($record->pegawai) {
                                    $recipients->push($record->pegawai);
                                }

                                if ($record->instruktur) {
                                    $recipients->push($record->instruktur);
                                }

                                if ($record->planner) {
                                    $recipients->push($record->planner);
                                }

                                $recipients = $recipients->merge(User::role('planner')->get());
                            } else {
                                if ($record->editor) {
                                    $recipients->push($record->editor);
                                }

                                $recipients = $recipients->merge(User::role('editor')->get());
                            }

                            $recipients = $recipients->merge(User::role('super_admin')->get());

                            Notification::make()
                                ->title('🛠️ Ada Revisi Konten!')
                                ->body("Konten '{$record->nama_kegiatan}' butuh perbaikan: {$data['catatan']}")
                                ->danger()
                                ->actions([
                                    NotificationAction::make('view')
                                        ->label('Buka Konten')
                                        ->url(ContentResource::getUrl('edit', ['record' => $record]))
                                        ->button()
                                        ->markAsRead()
                                        ->close(),
                                ])
                                ->sendToDatabase($recipients->unique('id'))
                                ->send();

                            Notification::make()
                                ->title('Permintaan Revisi Terkirim')
                                ->success()
                                ->send();
                        }),

                    Action::make('selesai')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-star')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(
                            fn($record) =>
                            Auth::user()->hasAnyRole(['super_admin', 'admin_platform'])
                                && $record->status === 'siap_publish'
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'selesai',
                                'admin_id' => Auth::id(),
                                'tanggal_posting' => now()->format('Y-m-d'),
                            ]);

                            $recipients = collect();

                            if ($record->pegawai) {
                                $recipients->push($record->pegawai);
                            }

                            if ($record->instruktur) {
                                $recipients->push($record->instruktur);
                            }

                            if ($record->planner) {
                                $recipients->push($record->planner);
                            }

                            if ($record->editor) {
                                $recipients->push($record->editor);
                            }

                            $recipients = $recipients->merge(User::role(['super_admin', 'admin_platform'])->get());

                            Notification::make()
                                ->title('🚀 KONTEN TELAH LIVE!')
                                ->body("Konten '{$record->nama_kegiatan}' sudah dipublikasi.")
                                ->success()
                                ->actions([
                                    NotificationAction::make('view')
                                        ->label('Lihat Konten')
                                        ->url(ContentResource::getUrl('view', ['record' => $record]))
                                        ->button()
                                        ->markAsRead()
                                        ->close(),
                                ])
                                ->sendToDatabase($recipients->unique('id'));

                            Notification::make()
                                ->title('Konten Live!')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Aksi Cepat')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),

                ViewAction::make()
                    ->hiddenLabel()
                    ->tooltip('Lihat Detail'),

                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Edit / Lanjutkan')
                    ->visible(
                        fn($record) =>
                        $record->status !== 'selesai'
                            || Auth::user()->hasRole('super_admin')
                    ),

                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip('Hapus')
                    ->visible(
                        fn($record) =>
                        Auth::user()->hasAnyRole(['super_admin', 'planner'])
                            && ($record->status !== 'selesai' || Auth::user()->hasRole('super_admin'))
                    ),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'menunggu_editor' => 'Menunggu Editor',
                        'revisi_editor' => 'Revisi Editor',
                        'revisi_planner' => 'Revisi Planner',
                        'siap_publish' => 'Siap Publish',
                        'selesai' => 'Selesai',
                    ]),

                SelectFilter::make('platforms')
                    ->relationship('platforms', 'name')
                    ->multiple()
                    ->label('Filter Platform'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('mark_as_done')
                        ->label('Tandai Selesai (Massal)')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update([
                            'status' => 'selesai',
                            'admin_id' => Auth::id(),
                            'tanggal_posting' => now()->format('Y-m-d'),
                        ])),
                ]),
            ])

            ->striped();
    }
}
