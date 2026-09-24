<?php

namespace App\Modules\TimSosmed\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\TimSosmed\Models\SocialSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookAuthController extends Controller
{
    private const GRAPH_VERSION = 'v25.0';

    /**
     * Mengarahkan pengguna ke halaman OAuth Facebook Business.
     */
    public function redirectToFacebook()
    {
        $appId = config('services.meta.app_id') ?: env('META_APP_ID');

        if (empty($appId)) {
            session()->flash('error', 'Meta App ID belum dikonfigurasi. Silakan atur META_APP_ID di .env atau melalui Pengaturan Koneksi Meta di halaman Statistik Medsos.');
            return redirect('/admin/statistik-medsos');
        }

        $redirectUri = url('/auth/facebook/callback');
        $scopes = [
            'instagram_basic',
            'instagram_manage_insights',
            'pages_show_list',
            'pages_read_engagement',
            'business_management',
        ];

        $params = http_build_query([
            'client_id' => $appId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'auth_type' => 'rerequest',
        ]);

        $url = 'https://www.facebook.com/' . self::GRAPH_VERSION . '/dialog/oauth?' . $params;

        return redirect()->away($url);
    }

    /**
     * Menangani callback dari Facebook OAuth, menukar code dengan token,
     * serta mendeteksi akun Instagram Bisnis @bpvppangkep secara otomatis.
     */
    public function handleFacebookCallback(Request $request)
    {
        if ($request->has('error') || $request->has('error_reason')) {
            $errorDesc = $request->get('error_description', $request->get('error_reason', 'Login Facebook dibatalkan.'));
            session()->flash('error', 'Otentikasi Facebook dibatalkan: ' . $errorDesc);
            return redirect('/admin/statistik-medsos');
        }

        $code = $request->get('code');
        if (empty($code)) {
            session()->flash('error', 'Kode otorisasi dari Facebook tidak ditemukan.');
            return redirect('/admin/statistik-medsos');
        }

        $appId = config('services.meta.app_id') ?: env('META_APP_ID');
        $appSecret = config('services.meta.app_secret') ?: env('META_APP_SECRET');
        $redirectUri = url('/auth/facebook/callback');

        if (empty($appId) || empty($appSecret)) {
            session()->flash('error', 'Meta App ID atau App Secret belum dikonfigurasi di server.');
            return redirect('/admin/statistik-medsos');
        }

        try {
            // 1. Tukar Code dengan Short-lived Access Token
            $tokenResponse = Http::get('https://graph.facebook.com/' . self::GRAPH_VERSION . '/oauth/access_token', [
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            if ($tokenResponse->failed()) {
                $errMsg = $tokenResponse->json('error.message', 'Gagal menukar kode otentikasi Facebook.');
                session()->flash('error', 'Meta OAuth Error: ' . $errMsg);
                return redirect('/admin/statistik-medsos');
            }

            $shortToken = $tokenResponse->json('access_token');

            // 2. Tukar menjadi Long-lived Access Token (berlaku ~60 hari)
            $longTokenResponse = Http::get('https://graph.facebook.com/' . self::GRAPH_VERSION . '/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'fb_exchange_token' => $shortToken,
            ]);

            $userAccessToken = $longTokenResponse->successful()
                ? ($longTokenResponse->json('access_token') ?? $shortToken)
                : $shortToken;

            // 3. Ambil daftar Halaman Facebook & Akun Instagram Bisnis yang tertaut
            $accountsResponse = Http::get('https://graph.facebook.com/' . self::GRAPH_VERSION . '/me/accounts', [
                'fields' => 'id,name,access_token,instagram_business_account{id,username,name,profile_picture_url,followers_count}',
                'access_token' => $userAccessToken,
            ]);

            $finalToken = $userAccessToken;
            $matchedIgId = null;
            $matchedIgUsername = null;

            if ($accountsResponse->successful()) {
                $pages = $accountsResponse->json('data', []);

                // Cari halaman yang memiliki akun Instagram Bisnis
                foreach ($pages as $page) {
                    if (!empty($page['instagram_business_account'])) {
                        $igAccount = $page['instagram_business_account'];
                        $username = strtolower($igAccount['username'] ?? '');

                        // Prioritaskan akun @bpvppangkep
                        if (str_contains($username, 'bpvppangkep') || $username === 'bpvppangkep') {
                            $matchedIgId = (string) $igAccount['id'];
                            $matchedIgUsername = $igAccount['username'];
                            // Page token tidak kedaluwarsa selama akses halaman tidak dicabut
                            if (!empty($page['access_token'])) {
                                $finalToken = $page['access_token'];
                            }
                            break;
                        }

                        // Simpan akun Instagram Bisnis pertama sebagai fallback jika @bpvppangkep belum cocok
                        if (!$matchedIgId) {
                            $matchedIgId = (string) $igAccount['id'];
                            $matchedIgUsername = $igAccount['username'] ?? null;
                            if (!empty($page['access_token'])) {
                                $finalToken = $page['access_token'];
                            }
                        }
                    }
                }
            }

            // Fallback ke config/env jika belum ditemukan otomatis dari Graph API
            if (!$matchedIgId) {
                $matchedIgId = config('services.meta.ig_user_id') ?: env('META_IG_USER_ID');
            }

            // 4. Simpan ke database
            SocialSetting::updateOrCreate(
                ['provider_name' => 'facebook'],
                [
                    'access_token' => $finalToken,
                    'ig_user_id' => $matchedIgId,
                ]
            );

            $accountLabel = $matchedIgUsername ? "@{$matchedIgUsername}" : ($matchedIgId ? "ID: {$matchedIgId}" : '@bpvppangkep');
            session()->flash('success', "Berhasil terhubung ke Meta! Akun Instagram {$accountLabel} aktif dan data langsung dimuat.");
            session()->flash('auto_load_medsos', true);

            return redirect('/admin/statistik-medsos');
        } catch (\Throwable $e) {
            Log::error('Facebook OAuth Callback Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Terjadi kesalahan saat menghubungkan Meta: ' . $e->getMessage());
            return redirect('/admin/statistik-medsos');
        }
    }
}
