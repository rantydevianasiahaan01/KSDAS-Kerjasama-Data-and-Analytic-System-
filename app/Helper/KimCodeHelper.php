<?php

namespace App\Helper;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorSVG;

class KimCodeHelper
{
    public static function expiryYear(int $angkatan, ?int $now = null): int
    {
        $now = $now ?? (int) date('Y');
        $base = $angkatan + 4;
        if ($base - $now === 1) {
            return $now + 2;
        }

        return $base;
    }

    public static function qrSvg(string $value): string
    {
        $options = new QROptions([
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
            'eccLevel' => EccLevel::M,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'scale' => 4,
            'drawLightModules' => true,
        ]);

        return self::svgFragment((new QRCode($options))->render($value));
    }

    public static function barcodeSvg(string $value): string
    {
        $svg = (new BarcodeGeneratorSVG())->getBarcode(
            $value,
            BarcodeGeneratorSVG::TYPE_CODE_128,
            2,
            70
        );

        return self::svgFragment($svg);
    }

    private static function svgFragment(string $svg): string
    {
        $svg = preg_replace('/<\?xml[^>]*\?>/i', '', $svg) ?? $svg;

        return trim($svg);
    }
}
