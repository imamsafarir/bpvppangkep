<?php

namespace App\Modules\TimSosmed\Http\Controllers;

use App\Modules\TimSosmed\Models\SocialSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Controller;
class FacebookAuthController extends Controller
{
    // public function redirectToFacebook()
    // {
    //     $appId = config('services.meta.app_id') ?? env('META_APP_ID');

    //     // KITA MATIKAN REDIRECT SEMENTARA UNTUK DEBUGGING
    //     echo "<h1>Hasil Debugging Meta App ID:</h1>";
    //     echo "App ID yang terbaca sistem: <b>'" . $appId . "'</b><br>";
    //     echo "Panjang karakter App ID: <b>" . strlen((string)$appId) . " karakter</b><br><br>";

    //     echo "<p>Keterangan:</p>";
    //     echo "<ul>";
    //     echo "<li>Jika kosong (''), berarti .env tidak terbaca atau belum di-clear cache.</li>";
    //     echo "<li>Jika ada angkanya, pastikan angka tersebut benar-benar App ID (bukan Business ID).</li>";
    //     echo "</ul>";

    //     die(); // Menghentikan proses secara paksa
    // }

    public function redirectToFacebook()
    {
        // Menggunakan fallback: coba ambil dari config, kalau kosong paksa ambil dari env()
        $appId = config('services.meta.app_id') ?: env('META_APP_ID');

        $redirectUri = url('/auth/facebook/callback');
        $scopes = 'instagram_basic,instagram_manage_insights,pages_show_list,pages_read_engagement';

        $url = "https://www.facebook.com/v25.0/dialog/oauth?client_id={$appId}&redirect_uri={$redirectUri}&scope={$scopes}";

        // Tahan sebentar untuk memastikan nilainya sudah terisi
        // dd('ID Terbaca di Server: ' . $appId);
    }

    public function handleFacebookCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/admin/statistik-medsos')->with('error', 'Login Facebook dibatalkan.');
        }

        $code = $request->code;
        $appId = env('META_APP_ID');
        $appSecret = env('META_APP_SECRET');
        $redirectUri = url('/auth/facebook/callback');

        // 1. Tukar Code dengan Short-lived Access Token
        $tokenResponse = Http::get("https://graph.facebook.com/v25.0/oauth/access_token", [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ]);

        $shortToken = $tokenResponse->json('access_token');

        // 2. (Opsional tapi disarankan) Tukar jadi Long-lived Access Token (Bisa awet 60 hari)
        $longTokenResponse = Http::get("https://graph.facebook.com/v25.0/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $shortToken,
        ]);

        $longToken = $longTokenResponse->json('access_token');

        // 3. Simpan token secara global ke tabel SocialSetting
        SocialSetting::updateOrCreate(
            ['provider_name' => 'facebook'], // Cari data facebook
            [
                'access_token' => $longToken,
                // Sementara kita bisa ambil ig_user_id dari .env atau fetch via API nanti
                'ig_user_id' => env('META_IG_USER_ID')
            ]
        );

        return redirect('/admin/statistik-medsos');
    }
}
