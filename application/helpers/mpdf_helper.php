<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function pdf_create($html, $filename, $stream = true, $css = false, $landscape = false)
{

    require_once APPPATH . 'helpers/mpdf/mpdf.php';

    if($landscape){
        $mpdf = new mPDF('c', 'A4-L');
    }else{
        $mpdf = new mPDF('c', 'A4');
    }

    if($css) {
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html, 2);
    } else {
        $mpdf->WriteHTML($html);

    }

    ob_clean();  // eh  aqui que a mágica acontece!  :)

    if ($stream) {
        $mpdf->Output($filename . '.pdf', 'I');
    } else {
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');

        return './uploads/temp/' . $filename . '.pdf';
    }

}
