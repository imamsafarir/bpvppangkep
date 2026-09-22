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

    /**
     * Export data pengunjung (leads) ke format CSV / Excel
     */
    public function exportLeadsCsv()
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'shortlink'])) {
            abort(403);
        }

        $query = ShortlinkLead::query()->with('shortlink')->latest();

        // Jika bukan superadmin, hanya unduh leads dari shortlink miliknya
        if ($user->role !== 'admin') {
            $query->whereHas('shortlink', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        $leads = $query->get();
        $filename = 'Data_Pengunjung_Shortlink_' . date('Y-m-d_His') . '.csv';

        $callback = function () use ($leads) {
            $handle = fopen('php://output', 'w');
            // Tambahkan BOM untuk kompatibilitas Microsoft Excel agar karakter UTF-8 terbaca rapi
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header Kolom
            fputcsv($handle, [
                'No',
                'Nama Pegawai / Pemilik Link',
                'Kode Shortlink',
                'Nama Pengunjung',
                'Nomor WhatsApp',
                'Email',
                'Alamat IP',
                'Waktu Akses',
            ], ';');

            foreach ($leads as $index => $lead) {
                fputcsv($handle, [
                    $index + 1,
                    $lead->shortlink?->pegawai_name ?? '-',
                    $lead->shortlink?->code ?? '-',
                    $lead->nama ?? '-',
                    $lead->whatsapp ? "'" . $lead->whatsapp : '-', // Beri kutip depan agar Excel tidak mengubah 08xx jadi angka hilang nolnya
                    $lead->email ?? '-',
                    $lead->ip_address ?? '-',
                    $lead->created_at ? $lead->created_at->format('d-m-Y H:i:s') : '-',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
