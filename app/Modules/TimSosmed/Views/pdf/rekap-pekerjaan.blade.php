<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: 8.5in 13in;
            /* Ukuran kertas Folio / F4 */
            margin: 40px 40px 60px 40px;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        .meta-info {
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .meta-info table {
            width: 100%;
            border: none;
            margin-top: 0;
        }

        .meta-info td {
            border: none;
            padding: 4px;
        }

        .chart-container {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            background-color: #ffffff;
        }

        .chart-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #4f46e5;
            text-align: center;
        }

        .css-chart td {
            border: none;
            padding: 4px;
            vertical-align: middle;
        }

        .css-chart .bar {
            background-color: #4f46e5;
            color: white;
            text-align: right;
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
            white-space: nowrap;
            min-width: 20px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        th {
            background-color: #4f46e5;
            color: white;
            padding: 10px 8px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #eee;
            padding: 8px;
            vertical-align: top;
        }

        .thumbnail {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            object-fit: cover;
        }

        .role-label {
            font-weight: bold;
            color: #4f46e5;
            font-size: 9px;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            width: 100%;
            text-align: right;
            font-size: 9px;
            color: #999;
        }

        .page-number:before {
            content: "Halaman " counter(page) " dari " counter(pages);
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="title">SosmedHub Production Report</h1>
        <p class="subtitle">{{ $title }}</p>
    </div>

    <div class="meta-info">
        @php
            $user = auth()->user();
            $roles = '-';
            if ($user && method_exists($user, 'roles')) {
                $roles =
                    $user->roles->pluck('name')->map(fn($r) => ucwords(str_replace('_', ' ', $r)))->join(', ') ?: '-';
            }
        @endphp
        <table>
            <tr>
                <td width="50%">
                    <strong>Diunduh oleh:</strong> {{ $user->name ?? 'Sistem' }} <br>
                    <strong>Email Pengguna:</strong> {{ $user->email ?? '-' }} <br>
                    <strong>Role Pengguna:</strong> {{ $roles }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Tanggal Unduh:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB <br>
                    <strong>Total Konten:</strong> {{ count($records) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="chart-container">
        <div class="chart-title">Distribusi Konten per Platform</div>
        @php
            $platformCounts = [];
            $maxCount = 0;
            foreach ($records as $r) {
                foreach ($r->platforms as $p) {
                    $platformCounts[$p->name] = ($platformCounts[$p->name] ?? 0) + 1;
                }
            }
            arsort($platformCounts);
            if (!empty($platformCounts)) {
                $maxCount = max($platformCounts);
            }
        @endphp

        @if ($maxCount > 0)
            <table class="css-chart" style="width: 100%; border: none; margin-top: 5px;">
                @foreach ($platformCounts as $name => $count)
                    <tr>
                        <td width="20%" style="font-weight: bold; font-size: 10px;">{{ $name }}</td>
                        <td width="80%">
                            <div class="bar" style="width: {{ max(($count / $maxCount) * 100, 3) }}%">
                                {{ $count }}</div>
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <p style="text-align: center; color: #666; font-size: 10px;">Tidak ada data platform untuk ditampilkan.</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">Media</th>
                <th width="20%">Judul Konten</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Tim Produksi</th>
                <th width="40%">Platform & Caption</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $row)
                <tr>
                    <td style="text-align: center;">
                        @php
                            $media = $row->getFirstMedia('hasil_edit') ?? $row->getFirstMedia('mentah');
                        @endphp
                        @if ($media && file_exists($media->getPath()))
                            <img src="{{ $media->getPath() }}" class="thumbnail">
                        @else
                            <div
                                style="background-color: #f3f4f6; width: 40px; height: 40px; display: inline-block; line-height: 40px; color: #9ca3af; font-size: 8px; border-radius: 4px;">
                                N/A</div>
                        @endif
                    </td>
                    <td style="font-weight: bold;">{{ $row->nama_kegiatan }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal_kegiatan)->format('d M Y') }}</td>

                    <td>
                        <span class="role-label">Planner:</span> {{ $row->planner?->name ?? '-' }} <br>
                        <span class="role-label">Editor:</span> {{ $row->editor?->name ?? '-' }} <br>
                        <span class="role-label">Admin:</span> {{ $row->admin?->name ?? '-' }}
                    </td>

                    <td>
                        <strong>Platforms:</strong> {{ $row->platforms->pluck('name')->join(', ') }}<br>
                        <div style="margin-top: 4px; color: #666;">
                            {!! Str::limit(strip_tags($row->caption), 100) !!}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen Otomatis SosmedHub Smart Management System - <span class="page-number"></span>
    </div>
</body>

</html>
