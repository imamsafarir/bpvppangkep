<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // 💡 Di-import untuk kebutuhan Rate Limiter

class CustomLogin extends BaseLogin
{
    /**
     * Menimpa method Email field agar menjadi Username field secara bersih
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }

    /**
     * Beritahu Filament untuk mengambil kredensial dari field 'username'
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    /**
     * Eksekusi autentikasi dan memunculkan notifikasi pop-up estetik saat gagal
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $e) {
            Notification::make()
                ->title('Gagal Masuk')
                ->body('Username atau password salah. Silakan coba lagi.')
                ->danger()
                ->icon('heroicon-o-x-circle') // Icon silang merah di tengah notifikasi
                ->send();

            throw $e;
        }
    }

    /**
     * 💡 PELENGKAP UTAMA: Mengalihkan pencatatan log pembatasan percobaan login (Throttling)
     * ke Username dengan parameter yang match dengan core Filament Anda.
     */
    protected function getRateLimitKey($method, $component = null)
    {
        return Str::transliterate(
            Str::lower($this->data['username'] ?? '') . '|' . request()->ip()
        );
    }
}
