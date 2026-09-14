<?php

namespace common\components;

use Yii;

require_once dirname(dirname(__DIR__)) . '/vendor/mpdf/mpdf/qrcode/qrcode.class.php';

class QrCodeHelper
{
    public static function generateSvg($text, $size = 200)
    {
        $qr = new \QRcode($text, 'M');
        $qrSize = $qr->getQrSize();
        $pixelSize = $size / $qrSize;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">';
        $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="#ffffff"/>';

        $reflection = new \ReflectionClass($qr);
        $finalProperty = $reflection->getProperty('final');
        $finalProperty->setAccessible(true);
        $finalMatrix = $finalProperty->getValue($qr);

        for ($y = 0; $y < $qrSize; $y++) {
            for ($x = 0; $x < $qrSize; $x++) {
                $index = $x + ($y * $qrSize) + 1;
                if (!empty($finalMatrix[$index])) {
                    $posX = $x * $pixelSize;
                    $posY = $y * $pixelSize;
                    $svg .= '<rect x="' . $posX . '" y="' . $posY . '" width="' . $pixelSize . '" height="' . $pixelSize . '" fill="#000000"/>';
                }
            }
        }

        $svg .= '</svg>';
        return $svg;
    }

    public static function generateDataUri($text, $size = 200)
    {
        $svg = self::generateSvg($text, $size);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function getPublicRakUrl($rakId, $customBaseUrl = null)
    {
        if (!empty($customBaseUrl)) {
            $base = rtrim($customBaseUrl, '/');
            return $base . '/opac/rak?id=' . urlencode($rakId);
        }

        $defaultBase = 'http://172.30.14.94/inlislite3';
        if (isset(Yii::$app->request) && method_exists(Yii::$app->request, 'getHostInfo')) {
            $hostInfo = Yii::$app->request->hostInfo;
            if (!empty($hostInfo) && strpos($hostInfo, 'localhost') === false && strpos($hostInfo, '127.0.0.1') === false) {
                $baseUrl = Yii::$app->request->baseUrl;
                $opacBase = preg_replace('/\/backend.*$/', '/opac', $baseUrl);
                if ($opacBase === $baseUrl) {
                    $opacBase = '/opac';
                }
                return $hostInfo . $opacBase . '/rak?id=' . urlencode($rakId);
            }
        }

        return $defaultBase . '/opac/rak?id=' . urlencode($rakId);
    }
}
