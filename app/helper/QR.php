<?php

include_once __DIR__ . '/../vendor/phpqrcode-master/qrlib.php';

class QR {
    public static function generarQr($url) {
        $directorioQR = __DIR__ . '/../public/qr_codes';
        
        if (!file_exists($directorioQR)) {
            mkdir($directorioQR, 0777, true);
        }
        $nombreArchivoQR = uniqid('qr_') . '.png';
        $archivoQR = $directorioQR . '/' . $nombreArchivoQR;

        QRcode::png($url, $archivoQR);
        return $nombreArchivoQR;
    }
}

