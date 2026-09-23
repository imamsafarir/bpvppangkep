<?php

require 'vendor/autoload.php';

use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;

$qr = Encoder::encode('https://example.com', ErrorCorrectionLevel::H());
$matrix = $qr->getMatrix();
$width = $matrix->getWidth();
$height = $matrix->getHeight();

echo "Matrix size: {$width}x{$height}\n";

// Render to GD image
$scale = 4;
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
$pngData = ob_get_clean();
imagedestroy($img);

echo "Pure PHP+GD PNG QR code created! Bytes: " . strlen($pngData) . "\n";
file_put_contents('test_qr.png', $pngData);
echo "File test_qr.png saved successfully!\n";
