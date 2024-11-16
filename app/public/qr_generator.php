<?php

include __DIR__ . '/../vendor/phpqrcode/qrlib.php';

$dir = __DIR__ . '/temp/';

if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

$username = isset($_GET['nombreUsuario']) ? $_GET['nombreUsuario'] : '';


$profileUrl = "http://localhost/PWll-TP-Final/app/usuario/showPerfilUsuario?nombreUsuario=" . urlencode($username);


$fileName = $dir . $username . '_qr.png';

QRcode::png($profileUrl, $fileName);

