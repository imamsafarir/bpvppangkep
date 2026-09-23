<?php

namespace App\Modules\Shortlink\Http\Controllers;

use App\Modules\Shortlink\Models\Shortlink;
use App\Modules\Shortlink\Models\ShortlinkLead;
use App\Modules\Shortlink\Services\ShortlinkExcelService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShortlinkController
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

        return view('shortlink::capture', compact('shortlink', 'fields'));
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
        return view('shortlink::thankyou', [
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

        if ($user->role !== 'admin') {
            $query->whereHas('shortlink', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        $leads = $query->get();

        return ShortlinkExcelService::exportLeads($leads);
    }

    /**
     * Download Template Excel/CSV untuk Import Data Shortlink
     */
    public function downloadTemplate()
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'shortlink'])) {
            abort(403);
        }

        $filename = 'Template_Import_Shortlink.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Nama Pegawai',
                'Tautan Tujuan Asli',
                'Aktifkan Pengambilan Data (YA/TIDAK)',
                'Pilihan Data (Pisahkan koma: nama,whatsapp,email)',
            ], ';');

            fputcsv($handle, [
                'Ahmad Dahlan, S.T.',
                'https://bpvppangkep.kemnaker.go.id/pelayanan',
                'YA',
                'nama,whatsapp',
            ], ';');

            fputcsv($handle, [
                'Siti Rahmawati, S.Kom',
                'https://drive.google.com/drive/folders/contoh',
                'TIDAK',
                '',
            ], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export data shortlink ke file Excel/CSV
     */
    public function exportShortlinksCsv()
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'shortlink'])) {
            abort(403);
        }

        $query = Shortlink::query()->with(['user'])->withCount('leads')->latest();

        if ($user->role !== 'admin') {
            $query->where('created_by', $user->id);
        }

        $shortlinks = $query->get();

        return ShortlinkExcelService::exportShortlinks($shortlinks);
    }

    /**
     * Import shortlink dari file Excel/CSV
     */
    public function importShortlinks(Request $request)
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'shortlink'])) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $rows = array_map(function ($line) {
            return str_getcsv($line, ';');
        }, file($path));

        if (isset($rows[0]) && count($rows[0]) === 1) {
            $rows = array_map(function ($line) {
                return str_getcsv($line, ',');
            }, file($path));
        }

        if (empty($rows)) {
            return back()->with('error', 'File template kosong!');
        }

        array_shift($rows);

        $insertedCount = 0;

        foreach ($rows as $row) {
            $pegawaiName      = isset($row[0]) ? trim($row[0]) : '';
            $destinationUrl   = isset($row[1]) ? trim($row[1]) : '';
            $isCaptureRaw     = isset($row[2]) ? strtoupper(trim($row[2])) : 'TIDAK';
            $captureFieldsRaw = isset($row[3]) ? trim($row[3]) : '';

            if (empty($pegawaiName) || empty($destinationUrl)) {
                continue;
            }

            if (! str_starts_with($destinationUrl, 'http://') && ! str_starts_with($destinationUrl, 'https://')) {
                $destinationUrl = 'https://' . $destinationUrl;
            }

            $isCaptureActive = in_array($isCaptureRaw, ['YA', 'YES', '1', 'TRUE', 'AKTIF']);

            $captureFields = [];
            if ($isCaptureActive && ! empty($captureFieldsRaw)) {
                foreach (explode(',', $captureFieldsRaw) as $field) {
                    $cleaned = strtolower(trim($field));
                    if (in_array($cleaned, ['nama', 'whatsapp', 'email'])) {
                        $captureFields[] = $cleaned;
                    }
                }
            }

            if ($isCaptureActive && empty($captureFields)) {
                $captureFields = ['nama', 'whatsapp'];
            }

            Shortlink::create([
                'pegawai_name'      => $pegawaiName,
                'code'              => Shortlink::generateUniqueCode(5),
                'destination_url'   => $destinationUrl,
                'is_capture_active' => $isCaptureActive,
                'capture_fields'    => $captureFields,
                'is_active'         => true,
                'created_by'        => $user->id,
            ]);

            $insertedCount++;
        }

        return redirect()->to(url('/admin/manage-shortlink'))
            ->with('success', "Berhasil mengimpor {$insertedCount} shortlink & barcode baru!");
    }
}
