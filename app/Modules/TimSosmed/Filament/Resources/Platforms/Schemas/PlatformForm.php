<?php

namespace App\Modules\TimSosmed\Filament\Resources\Platforms\Schemas;

use Filament\Forms\Components\TextInput; // Dari namespace Forms
use Filament\Forms\Components\Toggle;    // Dari namespace Forms
use Filament\Schemas\Components\Section; // Dari namespace Schemas
use Filament\Schemas\Components\Utilities\Set;             // Objek Schema Utama
use Filament\Schemas\Schema; // Utility Set terbaru
use Illuminate\Support\Str;

class PlatformForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Platform')
                    ->description('Sesuaikan identitas dan ikon platform sosial media.')
                    ->schema([ // Section menggunakan method schema() untuk menampung field
                        TextInput::make('name')
                            ->label('Nama Platform')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug / ID')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('otomatis-terisi')
                            ->helperText('Identitas unik untuk sistem.'),

                        TextInput::make('icon')
                            ->label('Icon Heroicon')
                            ->placeholder('heroicon-o-share')
                            ->helperText('Contoh: heroicon-o-camera'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
