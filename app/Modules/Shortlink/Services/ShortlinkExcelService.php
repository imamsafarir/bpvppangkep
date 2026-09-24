<?php

namespace App\Modules\Shortlink\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ShortlinkExcelService
{
    /**
     * Generate PNG QR code using BaconQrCode matrix and pure PHP GD
     */
    public static function generateQrPng(string $text, int $scale = 3): string
    {
        $oldLevel = error_reporting(0);
        try {
            $qr = Encoder::encode($text, ErrorCorrectionLevel::M());
        } finally {
            error_reporting($oldLevel);
        }

        $matrix = $qr->getMatrix();
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        $margin = 2;
        $imgWidth = ($width + $margin * 2) * $scale;
        $imgHeight = ($height + $margin * 2) * $scale;

        $img = imagecreatetruecolor($imgWidth, $imgHeight);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        imagefill($img, 0, 0, $white);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    imagefilledrectangle(
                        $img,
                        ($x + $margin) * $scale,
                        ($y + $margin) * $scale,
                        ($x + $margin + 1) * $scale - 1,
                        ($y + $margin + 1) * $scale - 1,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    /**
     * Build standard OpenXML (.xlsx) spreadsheet with embedded images
     *
     * @param string $sheetName
     * @param array $headers
     * @param array $rows
     * @param array $images Keyed by row index (1-based, e.g. 2 for first data row): ['col' => int (0-based), 'data' => string]
     * @return string Path to temporary .xlsx file
     */
    public static function buildXlsxFile(string $sheetName, array $headers, array $rows, array $images = []): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_') . '.xlsx';
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Tidak dapat membuat file temporary Excel di: {$tempFile}");
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $contentTypes .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $contentTypes .= '<Default Extension="xml" ContentType="application/xml"/>';
        $contentTypes .= '<Default Extension="png" ContentType="image/png"/>';
        $contentTypes .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $contentTypes .= '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        $contentTypes .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        if (! empty($images)) {
            $contentTypes .= '<Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>';
        }
        $contentTypes .= '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $rels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $rels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>';
        $rels .= '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/styles.xml
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $styles .= '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $styles .= '<fonts count="2">';
        $styles .= '<font><sz val="11"/><name val="Calibri"/></font>';
        $styles .= '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>';
        $styles .= '</fonts>';
        $styles .= '<fills count="3">';
        $styles .= '<fill><patternFill patternType="none"/></fill>';
        $styles .= '<fill><patternFill patternType="gray125"/></fill>';
        $styles .= '<fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/></patternFill></fill>'; // Dark navy header
        $styles .= '</fills>';
        $styles .= '<borders count="2">';
        $styles .= '<border><left/><right/><top/><bottom/></border>';
        $styles .= '<border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom></border>';
        $styles .= '</borders>';
        $styles .= '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>';
        $styles .= '<cellXfs count="3">';
        $styles .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'; // Normal cell
        $styles .= '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'; // Header
        $styles .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'; // Center cell
        $styles .= '</cellXfs>';
        $styles .= '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 4. xl/workbook.xml
        $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wb .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $wb .= '<sheets>';
        $wb .= '<sheet name="' . htmlspecialchars($sheetName, ENT_XML1) . '" sheetId="1" r:id="rId1"/>';
        $wb .= '</sheets>';
        $wb .= '</workbook>';
        $zip->addFromString('xl/workbook.xml', $wb);

        // 5. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wbRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $wbRels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>';
        $wbRels .= '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        $wbRels .= '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 6. xl/worksheets/sheet1.xml
        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sheet .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        // Column widths
        $sheet .= '<cols>';
        $colCount = count($headers);
        for ($c = 1; $c <= $colCount; $c++) {
            $headerName = $headers[$c - 1];
            if (str_contains(strtolower($headerName), 'barcode') || str_contains(strtolower($headerName), 'qr')) {
                $width = 17;
            } elseif ($headerName === 'No') {
                $width = 8;
            } else {
                $width = 25;
            }
            $sheet .= '<col min="' . $c . '" max="' . $c . '" width="' . $width . '" customWidth="1"/>';
        }
        $sheet .= '</cols>';

        $sheet .= '<sheetData>';

        // Header row
        $sheet .= '<row r="1" ht="28" customHeight="1">';
        foreach ($headers as $colIdx => $h) {
            $colLetter = self::columnLetter($colIdx);
            $sheet .= '<c r="' . $colLetter . '1" t="inlineStr" s="1"><is><t>' . htmlspecialchars($h, ENT_XML1) . '</t></is></c>';
        }
        $sheet .= '</row>';

        // Data rows
        $rowIdx = 2;
        foreach ($rows as $row) {
            $hasImg = ! empty($images[$rowIdx]);
            $rowHeight = $hasImg ? 78 : 24;
            $sheet .= '<row r="' . $rowIdx . '" ht="' . $rowHeight . '" customHeight="1">';
            foreach ($row as $colIdx => $val) {
                $colLetter = self::columnLetter($colIdx);
                $headerName = $headers[$colIdx] ?? '';
                $style = ($headerName === 'No' || str_contains(strtolower($headerName), 'barcode') || str_contains(strtolower($headerName), 'qr')) ? '2' : '0';
                $sheet .= '<c r="' . $colLetter . $rowIdx . '" t="inlineStr" s="' . $style . '"><is><t>' . htmlspecialchars((string) $val, ENT_XML1) . '</t></is></c>';
            }
            $sheet .= '</row>';
            $rowIdx++;
        }
        $sheet .= '</sheetData>';

        if (! empty($images)) {
            $sheet .= '<drawing r:id="rId1"/>';
        }
        $sheet .= '</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

        // 7. xl/worksheets/_rels/sheet1.xml.rels & xl/drawings
        if (! empty($images)) {
            $sheetRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
            $sheetRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
            $sheetRels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/>';
            $sheetRels .= '</Relationships>';
            $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', $sheetRels);

            $drRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
            $drRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

            $drXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
            $drXml .= '<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';

            $imgId = 1;
            foreach ($images as $rowNum => $imgInfo) {
                $colNum = $imgInfo['col']; // 0-based column
                $pngContent = $imgInfo['data'];
                $imgPath = 'xl/media/image' . $imgId . '.png';
                $zip->addFromString($imgPath, $pngContent);

                $rId = 'rId' . $imgId;
                $drRels .= '<Relationship Id="' . $rId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image' . $imgId . '.png"/>';

                // Embed image centered inside the cell
                $drXml .= '<xdr:oneCellAnchor>';
                $drXml .= '<xdr:from>';
                $drXml .= '<xdr:col>' . $colNum . '</xdr:col>';
                $drXml .= '<xdr:colOff>180000</xdr:colOff>';
                $drXml .= '<xdr:row>' . ($rowNum - 1) . '</xdr:row>';
                $drXml .= '<xdr:rowOff>80000</xdr:rowOff>';
                $drXml .= '</xdr:from>';
                $drXml .= '<xdr:ext cx="762000" cy="762000"/>'; // 80x80 px in EMUs
                $drXml .= '<xdr:pic>';
                $drXml .= '<xdr:nvPicPr>';
                $drXml .= '<xdr:cNvPr id="' . ($imgId + 1) . '" name="QR_' . $imgId . '"/>';
                $drXml .= '<xdr:cNvPicPr><a:picLocks noChangeAspect="1"/></xdr:cNvPicPr>';
                $drXml .= '</xdr:nvPicPr>';
                $drXml .= '<xdr:blipFill>';
                $drXml .= '<a:blip xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:embed="' . $rId . '"/>';
                $drXml .= '<a:stretch><a:fillRect/></a:stretch>';
                $drXml .= '</xdr:blipFill>';
                $drXml .= '<xdr:spPr>';
                $drXml .= '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>';
                $drXml .= '</xdr:spPr>';
                $drXml .= '</xdr:pic>';
                $drXml .= '<xdr:clientData/>';
                $drXml .= '</xdr:oneCellAnchor>';

                $imgId++;
            }

            $drXml .= '</xdr:wsDr>';
            $drRels .= '</Relationships>';

            $zip->addFromString('xl/drawings/drawing1.xml', $drXml);
            $zip->addFromString('xl/drawings/_rels/drawing1.xml.rels', $drRels);
        }

        $zip->close();

        return $tempFile;
    }

    /**
     * Convert 0-based column index to Excel letter (A, B, ..., Z, AA, AB, ...)
     */
    private static function columnLetter(int $colIndex): string
    {
        $letter = '';
        $colIndex++;
        while ($colIndex > 0) {
            $modulo = ($colIndex - 1) % 26;
            $letter = chr(65 + $modulo) . $letter;
            $colIndex = (int) (($colIndex - $modulo) / 26);
        }

        return $letter;
    }

    /**
     * Export shortlinks with embedded QR code image in Excel
     */
    public static function exportShortlinks(Collection $shortlinks): BinaryFileResponse
    {
        $headers = [
            'No',
            'Nama Pegawai',
            'Kode Shortlink',
            'Barcode / QR Code',
            'Tautan Shortlink',
            'Tautan Tujuan Asli',
            'Total Klik',
            'Total Data Masuk',
            'Form Pengambilan Data',
            'Field Diminta',
            'Judul Header Kustom',
            'Teks Arahan Kustom',
            'Teks Tombol Kustom',
            'Dibuat Oleh',
            'Tanggal Dibuat',
        ];

        $rows = [];
        $images = [];
        $rowNum = 2; // Data starts at row 2

        foreach ($shortlinks as $index => $item) {
            $fields = is_array($item->capture_fields) ? implode(', ', $item->capture_fields) : '-';
            $rows[] = [
                $index + 1,
                $item->pegawai_name,
                $item->code,
                '', // Kolom Barcode / QR Code (gambar disematkan di sini)
                $item->short_url,
                $item->destination_url,
                $item->clicks_count,
                $item->leads_count,
                $item->is_capture_active ? 'Aktif' : 'Nonaktif',
                $fields,
                $item->custom_title ?: '-',
                $item->custom_description ?: '-',
                $item->custom_button_text ?: '-',
                $item->user?->name ?? '-',
                $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-',
            ];

            // Generate PNG QR code
            try {
                $qrPng = self::generateQrPng($item->short_url);
                $images[$rowNum] = [
                    'col'  => 3, // Column D (0-based index 3)
                    'data' => $qrPng,
                ];
            } catch (\Throwable $e) {
                // Safe fallback if QR generation fails
            }

            $rowNum++;
        }

        $filename = 'Data_Shortlink_Pegawai_' . date('Y-m-d_His') . '.xlsx';
        $tempPath = self::buildXlsxFile('Data Shortlink', $headers, $rows, $images);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export leads with embedded QR code image in Excel
     */
    public static function exportLeads(Collection $leads): BinaryFileResponse
    {
        $headers = [
            'No',
            'Nama Pegawai / Pemilik Link',
            'Kode Shortlink',
            'Barcode / QR Code',
            'Tautan Shortlink',
            'Judul Formulir',
            'Nama Pengunjung',
            'Nomor WhatsApp',
            'Email',
            'Alamat IP',
            'Waktu Akses',
        ];

        $rows = [];
        $images = [];
        $rowNum = 2;

        foreach ($leads as $index => $lead) {
            $shortlink = $lead->shortlink;
            $shortUrl = $shortlink ? $shortlink->short_url : url('/s/' . ($lead->code ?? ''));

            $rows[] = [
                $index + 1,
                $shortlink?->pegawai_name ?? '-',
                $shortlink?->code ?? '-',
                '', // Kolom Barcode / QR Code
                $shortUrl,
                $shortlink?->display_title ?? '-',
                $lead->nama ?? '-',
                $lead->whatsapp ? $lead->whatsapp : '-',
                $lead->email ?? '-',
                $lead->ip_address ?? '-',
                $lead->created_at ? $lead->created_at->format('d-m-Y H:i:s') : '-',
            ];

            if ($shortlink) {
                try {
                    $qrPng = self::generateQrPng($shortUrl);
                    $images[$rowNum] = [
                        'col'  => 3, // Column D
                        'data' => $qrPng,
                    ];
                } catch (\Throwable $e) {
                    // Safe fallback
                }
            }

            $rowNum++;
        }

        $filename = 'Data_Pengunjung_Shortlink_' . date('Y-m-d_His') . '.xlsx';
        $tempPath = self::buildXlsxFile('Data Pengunjung', $headers, $rows, $images);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
