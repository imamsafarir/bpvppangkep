<?php

namespace App\Modules\TimSosmed\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveTeamWidget extends BaseWidget
{
    // Mengatur posisi widget
    protected static ?int $sort = 4;

    // Judul yang menegaskan bahwa ini adalah fitur mendatang
    protected static ?string $heading = '⚡ Active Workspace (Status Tim - Next Feature)';

    public function table(Table $table): Table
    {
        return $table
            // Tetap mengambil data user sebagai placeholder
            ->query(User::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Anggota')
                    // Memberikan info bahwa fitur sedang dikembangkan lewat description
                    ->description('Sistem tracking real-time dalam tahap pengembangan.')
                    ->weight('bold')
                    ->color('gray'), // Memberikan efek "disabled" (abu-abu)

                Tables\Columns\TextColumn::make('email')
                    ->label('Kontak Email')
                    ->icon('heroicon-m-envelope')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Akses Fitur')
                    // Mengunci status dengan teks statis
                    ->getStateUsing(fn () => '🔒 Coming Soon')
                    ->badge()
                    ->color('gray') // Warna netral untuk kesan belum aktif
                    ->icon('heroicon-m-clock'),
            ])
            // Mematikan interaksi tabel
            ->paginated(false)
            // Menghilangkan garis baris agar lebih minimalis sebagai placeholder
            ->striped(false);
    }
}
