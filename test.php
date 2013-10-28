<?php

$dir = getcwd();
chdir('./vendor/tcpdf/examples');
require_once 'tcpdf_include.php';
chdir($dir);

function generate_file($i = 0) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $html = "Hello world!";
    
    $pdf->AddPage();
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->SetProtection([], 'testpassword', null, 3);
    $pdf->Output("files/example_$i.pdf", 'F');    
}

$start = microtime(true);
do {
    generate_file(++$count);
} while (microtime(true) - $start <= 1);

echo $count - 1, "\n";
