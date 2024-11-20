<?php

require_once __DIR__ . '/../vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

class PDFHelper {
    public static function generarPDF($html) {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("estadisticas.pdf", ['Attachment' => 1]);
    }
}