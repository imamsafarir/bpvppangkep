<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PlatformsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Platform')
                    ->icon(function ($record) {
                        $icon = $record->icon;
                        if (filled($icon) && str_starts_with($icon, 'heroicon-')) {
                            return $icon;
                        }

                        return match (strtolower($record->slug ?? $record->name ?? '')) {
                            'instagram' => 'heroicon-o-camera',
                            'facebook' => 'heroicon-o-user-group',
                            'tiktok' => 'heroicon-o-musical-note',
                            'youtube' => 'heroicon-o-video-camera',
                            'twitter', 'x' => 'heroicon-o-chat-bubble-left-right',
                            default => 'heroicon-o-globe-alt',
                        };
                    })
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug / ID')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('icon')
                    ->label('Icon Heroicon')
                    ->badge()
                    ->color('info')
                    ->placeholder('-')
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Status Platform Diperbarui')
                            ->body("Platform '{$record->name}' berhasil di-" . ($state ? 'aktifkan' : 'nonaktifkan') . '.')
                            ->success()
                            ->send();
                    })
                    ->sortable(),

                TextColumn::make('contents_count')
                    ->label('Total Konten')
                    ->counts('contents')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Filter Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Platform?')
                    ->modalDescription('Pastikan platform ini tidak sedang digunakan oleh konten aktif sebelum menghapusnya.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('aktifkan_semua')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => true]);
                            Notification::make()
                                ->title('Platform Berhasil Diaktifkan')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('nonaktifkan_semua')
                        ->label('Nonaktifkan Terpilih')
                        ->icon('heroicon-m-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => false]);
                            Notification::make()
                                ->title('Platform Berhasil Dinonaktifkan')
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->striped();
    }
}
