<?php

namespace App\Modules\TimSosmed\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                // ---> TAMBAHKAN INPUT USERNAME DI SINI <---
                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(ignoreRecord: true) // Pastikan username tidak boleh kembar
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    // Password hanya wajib diisi saat membuat user baru
                    ->required(fn (string $operation): bool => $operation === 'create')
                    // Otomatis mengenkripsi password sebelum disimpan
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),

                // FITUR UTAMA: Memilih Role dari Spatie
                Select::make('roles')
                    ->label('Hak Akses / Role')
                    ->multiple() // User bisa punya lebih dari 1 role jika perlu
                    ->relationship('roles', 'name') // Mengambil data dari tabel roles
                    ->preload() // Meload data role agar tidak berat saat diklik
                    ->required(),
            ]);
    }
}
