<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once 'mpdf/autoload.php';

use Mpdf\Mpdf;

class mPdf_helper extends Mpdf
{
	public function __construct()
	{
		 parent::__construct();
	} 
}

function pdf_create($html, $filename, $stream = true, $title = false, $css = false, $header = false, $footer = false, $landscape = false)
{

//    require_once APPPATH . 'libraries/mpdf/src/Mpdf.php';

    if($landscape){
        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
    }else{
        $mpdf = new \Mpdf\Mpdf();
    }

    if($header) {
        $mpdf->SetHTMLHeader($header, 'O', true);
    }

    if($footer) {
        $mpdf->SetHTMLFooter($footer, 'O');
    }

    if($css) {
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html, 2);
    } else {
        $mpdf->WriteHTML($html);

    }
    if($title) {
        $mpdf->SetTitle($title);
    }

    $mpdf->simpleTables = true;
    $mpdf->useSubstitutions = false;
//    ob_clean();  // eh  aqui que a mágica acontece!  :)

    if ($stream) {
        $mpdf->Output($filename . '.pdf', 'I');
    } else {
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');

        return './uploads/temp/' . $filename . '.pdf';
    }

}

