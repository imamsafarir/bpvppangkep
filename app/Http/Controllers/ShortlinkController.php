<?php

namespace App\Http\Controllers;

use App\Models\Shortlink;
use App\Models\ShortlinkLead;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShortlinkController extends Controller
{
    /**
     * Handle shortlink redirection or lead capture form
     */
    public function handle(Request $request, string $code)
    {
        $shortlink = Shortlink::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        // Jika toggle ambil data TIDAK aktif -> langsung redirect
        if (! $shortlink->is_capture_active) {
            $shortlink->increment('clicks_count');

            return redirect()->away($shortlink->destination_url);
        }

        // Jika toggle ambil data AKTIF -> tampilkan form capture
        $fields = $shortlink->capture_fields ?? ['nama'];

        return view('shortlinks.capture', compact('shortlink', 'fields'));
    }

    /**
     * Handle lead form submission
     */
    public function submit(Request $request, string $code)
    {
        $shortlink = Shortlink::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        $fields = $shortlink->capture_fields ?? [];

        // Dinamiskan validasi sesuai toggle field yang diaktifkan
        $rules = [];
        if (in_array('nama', $fields)) {
            $rules['nama'] = 'required|string|max:255';
        }
        if (in_array('whatsapp', $fields)) {
            $rules['whatsapp'] = 'required|string|max:30';
        }
        if (in_array('email', $fields)) {
            $rules['email'] = 'required|email|max:255';
        }

        $validated = $request->validate($rules);

        // Simpan data pengunjung
        ShortlinkLead::create([
            'shortlink_id' => $shortlink->id,
            'nama'         => $validated['nama'] ?? null,
            'whatsapp'     => $validated['whatsapp'] ?? null,
            'email'        => $validated['email'] ?? null,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        $shortlink->increment('clicks_count');

        // Tampilkan halaman terima kasih dengan auto-redirect
        return view('shortlinks.thankyou', [
            'destinationUrl' => $shortlink->destination_url,
            'pegawaiName'    => $shortlink->pegawai_name,
        ]);
    }

    /**
     * Download QR code as SVG
     */
    public function downloadQr(string $code)
    {
        $shortlink = Shortlink::where('code', $code)->firstOrFail();
        $url = $shortlink->short_url;

        $qrCode = QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($url);

        $filename = 'QR_' . str_replace(' ', '_', $shortlink->pegawai_name) . '_' . $code . '.svg';

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
