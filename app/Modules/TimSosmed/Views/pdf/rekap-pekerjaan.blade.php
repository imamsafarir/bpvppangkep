<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: 8.5in 13in;
            /* Ukuran Folio / F4 */
            margin: 35px 40px 45px 40px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.4;
        }

        /* KOP SURAT RESMI KEMNAKER / BPVP PANGKEP */
        .kop-header {
            width: 100%;
            margin-bottom: 8px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }

        .kop-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .kop-emblem {
            width: 70px;
            text-align: center;
        }

        .kop-text {
            text-align: center;
            padding-left: 10px;
        }

        .kop-title-1 {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
        }

        .kop-title-2 {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 1px 0 0 0;
        }

        .kop-title-3 {
            font-size: 12.5pt;
            font-weight: 900;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 2px 0 0 0;
        }

        .kop-address {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 3px;
            line-height: 1.25;
        }

        /* GARIS PEMBATAS GANDA KOP SURAT */
        .kop-divider-thick {
            height: 2.5px;
            background-color: #0f172a;
            margin-top: 6px;
            margin-bottom: 1.5px;
        }

        .kop-divider-thin {
            height: 0.8px;
            background-color: #0f172a;
            margin-bottom: 14px;
        }

        /* JUDUL DOKUMEN */
        .doc-title-box {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-main-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .doc-sub-title {
            font-size: 9pt;
            font-weight: bold;
            color: #4338ca;
            margin-top: 3px;
        }

        /* METADATA SUMMARY */
        .meta-summary-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 14px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }

        .meta-table td {
            border: none;
            padding: 2.5px 4px;
            font-size: 8.5pt;
            vertical-align: top;
        }

        /* TABEL DATA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8.5pt;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table th {
            background-color: #1e1b4b;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 6px;
            border: 1px solid #1e1b4b;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            vertical-align: top;
            line-height: 1.35;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 1.5px 5px;
            border-radius: 3px;
            margin-bottom: 2px;
        }

        .badge-type {
            background-color: #e0e7ff;
            color: #3730a3;
            border: 0.5px solid #c7d2fe;
        }

        .badge-platform {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 0.5px solid #cbd5e1;
            margin-right: 3px;
        }

        .role-tag {
            font-size: 7.5pt;
            line-height: 1.3;
        }

        .role-tag strong {
            color: #475569;
            font-size: 7pt;
            text-transform: uppercase;
        }

        .thumbnail-img {
            width: 44px;
            height: 44px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
        }

        .thumbnail-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 4px;
            background-color: #e2e8f0;
            color: #64748b;
            font-size: 7.5pt;
            text-align: center;
            line-height: 44px;
            display: inline-block;
            border: 1px dashed #94a3b8;
        }

        /* LEMBAR PENGESAHAN */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 0 15px;
            font-size: 9pt;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        .signature-nip {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            height: 18px;
            font-size: 7.5pt;
            color: #64748b;
            border-top: 0.8px solid #cbd5e1;
            padding-top: 4px;
        }

        .footer table {
            width: 100%;
            border: none;
            margin: 0;
        }

        .footer td {
            border: none;
            padding: 0;
            font-size: 7.5pt;
            color: #64748b;
        }

        .page-num:before {
            content: "Halaman " counter(page);
        }
    </style>
</head>

<body>
    {{-- FOOTER OTOMATIS DI SEMUA HALAMAN --}}
    <div class="footer">
        <table>
            <tr>
                <td style="text-align: left;">
                    Dokumen Resmi Pelaporan Media Sosial • BPVP Pangkep Kemnaker RI
                </td>
                <td style="text-align: right;" class="page-num"></td>
            </tr>
        </table>
    </div>

    {{-- KOP SURAT RESMI INSTANSI PEMERINTAH --}}
    <div class="kop-header">
        <table class="kop-table">
            <tr>
                <td class="kop-emblem">
                    {{-- Emblem / Ikon Lembaga --}}
                    <div style="font-size: 30pt; line-height: 1;">🏛️</div>
                </td>
                <td class="kop-text">
                    <div class="kop-title-1">KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA</div>
                    <div class="kop-title-2">DIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS</div>
                    <div class="kop-title-3">BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS PANGKEP</div>
                    <div class="kop-address">
                        Jalan Pengayoman No. 1, Pangkajene, Kabupaten Pangkajene dan Kepulauan, Sulawesi Selatan
                        90611<br>
                        Laman: https://bpvppangkep.kemnaker.go.id • Pos-el: bpvp.pangkep@kemnaker.go.id • Media Sosial:
                        <strong>@bpvppangkep</strong>
                    </div>
                </td>
            </tr>
        </table>
        <div class="kop-divider-thick"></div>
        <div class="kop-divider-thin"></div>
    </div>

    {{-- JUDUL DOKUMEN LAPORAN --}}
    <div class="doc-title-box">
        <h1 class="doc-main-title">LAPORAN REKAPITULASI PUBLIKASI MEDIA SOSIAL</h1>
        <div class="doc-sub-title">{{ $periodeLabel ?? $title }}</div>
    </div>

    {{-- METADATA SUMMARY BOX --}}
    <div class="meta-summary-box">
        <table class="meta-table">
            <tr>
                <td width="55%">
                    <strong>Unit Kerja:</strong> Tim Media Sosial & Informasi Publik BPVP Pangkep<br>
                    <strong>Pengunduh / Pencetak:</strong> {{ auth()->user()?->name ?? 'Sistem' }}
                    ({{ auth()->user()?->email ?? '-' }})<br>
                    <strong>Waktu Cetak:</strong> {{ $tanggalCetak ?? now()->translatedFormat('d F Y, H:i') . ' WITA' }}
                </td>
                <td width="45%">
                    <strong>Total Konten Publikasi:</strong> {{ count($records) }} Konten Tayang<br>
                    <strong>Status Konten:</strong> Selesai Terpublikasi (Live)<br>
                    @php
                        $platformList = [];
                        foreach ($records as $r) {
                            foreach ($r->platforms as $p) {
                                $platformList[$p->name] = ($platformList[$p->name] ?? 0) + 1;
                            }
                        }
                        $platformSummaryText =
                            collect($platformList)->map(fn($cnt, $nama) => "{$nama}: {$cnt}")->join(', ') ?: '-';
                    @endphp
                    <strong>Distribusi Kanal:</strong> {{ $platformSummaryText }}
                </td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA PUBLIKASI RINCI --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="8%" class="text-center">Media</th>
                <th width="26%">Judul & Format Konten</th>
                <th width="14%">Tanggal</th>
                <th width="24%">Tim Produksi</th>
                <th width="24%">Platform & Tautan Postingan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $row)
                @php
                    $media = $row->getFirstMedia('hasil_edit') ?? $row->getFirstMedia('mentah');
                    $plannerName = $row->instruktur?->name ?? ($row->planner?->name ?? '-');
                    $editorName = $row->editor?->name ?? '-';
                    $adminName = $row->admin?->name ?? '-';
                    $formatName = $row->jenis_konten ? ucfirst($row->jenis_konten) : 'Publikasi';
                @endphp
                <tr>
                    <td class="text-center" style="font-weight: bold; color: #475569;">
                        {{ $index + 1 }}
                    </td>

                    <td class="text-center">
                        @if ($media && file_exists($media->getPath()))
                            <img src="{{ $media->getPath() }}" class="thumbnail-img" alt="Thumbnail">
                        @else
                            <div class="thumbnail-placeholder">Aset</div>
                        @endif
                    </td>

                    <td>
                        <div class="text-bold" style="color: #0f172a; margin-bottom: 3px;">
                            {{ $row->nama_kegiatan }}
                        </div>
                        <span class="badge badge-type">{{ $formatName }}</span>
                        @if ($row->caption)
                            <div style="font-size: 7.5pt; color: #64748b; margin-top: 3px; font-style: italic;">
                                "{{ \Illuminate\Support\Str::limit(strip_tags($row->caption), 80) }}"
                            </div>
                        @endif
                    </td>

                    <td>
                        <div><strong>Publikasi:</strong></div>
                        <div style="color: #059669; font-weight: bold;">
                            {{ $row->tanggal_posting ? \Carbon\Carbon::parse($row->tanggal_posting)->translatedFormat('d M Y') : '-' }}
                        </div>
                        <div style="font-size: 7.5pt; color: #64748b; margin-top: 3px;">
                            Kegiatan:
                            {{ $row->tanggal_kegiatan ? \Carbon\Carbon::parse($row->tanggal_kegiatan)->translatedFormat('d M Y') : '-' }}
                        </div>
                    </td>

                    <td>
                        <div class="role-tag"><strong>Planner:</strong> {{ $plannerName }}</div>
                        <div class="role-tag"><strong>Editor:</strong> {{ $editorName }}</div>
                        <div class="role-tag"><strong>Admin:</strong> {{ $adminName }}</div>
                    </td>

                    <td>
                        <div style="margin-bottom: 4px;">
                            @forelse ($row->platforms as $p)
                                <span class="badge badge-platform">{{ $p->name }}</span>
                            @empty
                                <span style="color: #94a3b8; font-size: 7.5pt;">-</span>
                            @endforelse
                        </div>

                        @if (!empty($row->link_postingan))
                            <div style="font-size: 7.5pt; word-break: break-all;">
                                <a href="{{ $row->link_postingan }}"
                                    style="color: #2563eb; text-decoration: underline;" target="_blank">
                                    {{ \Illuminate\Support\Str::limit($row->link_postingan, 38) }}
                                </a>
                            </div>
                        @else
                            <span style="font-size: 7.5pt; color: #94a3b8; font-style: italic;">Belum ditautkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data konten yang sesuai dengan filter laporan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- LEMBAR PENGESAHAN / KOLOM TANDA TANGAN RESMI --}}
    @if (!empty($sertakanTandaTangan))
        <table class="signature-table">
            <tr>
                <td width="50%">
                    <div>Mengetahui,</div>
                    <div style="font-weight: bold; color: #0f172a; margin-top: 2px;">
                        {{ $namaPejabat ?? 'Subkoordinator Pemberdayaan Pelatihan' }}
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        {{ !empty($pejabatNama) ? $pejabatNama : '( .................................................... )' }}
                    </div>
                    <div class="signature-nip">
                        {{ !empty($nipPejabat) ? 'NIP. ' . $nipPejabat : 'NIP. ............................................' }}
                    </div>
                </td>

                <td width="50%">
                    <div>Pangkep, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="font-weight: bold; color: #0f172a; margin-top: 2px;">
                        Penanggung Jawab Tim Media Sosial
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        {{ auth()->user()?->name ?? 'Tim Media Sosial BPVP Pangkep' }}
                    </div>
                    <div class="signature-nip">
                        Balai Pelatihan Vokasi dan Produktivitas Pangkep
                    </div>
                </td>
            </tr>
        </table>
    @endif
</body>

</html>
