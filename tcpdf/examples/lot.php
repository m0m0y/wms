<?php

$stock_lotno = (isset($_GET['stock_lotno']) && !empty($_GET['stock_lotno'])) ? $_GET['stock_lotno'] : '404-NOTFOUND';

require_once('tcpdf_include.php');

// 3.5 x 2.2 in
 
$thermalSize = array(101.6, 63.5);


$pdf = new TCPDF('H', 'mm', $thermalSize, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('Barcode - '. $stock_lotno);
$pdf->SetSubject('Invoice barcode');

$pdf->SetPrintFooter(false);
$pdf->SetPrintHeader(false);

$size = 7;
$pdf->SetMargins($size, $size, $size);
$pdf->SetHeaderMargin($size);
$pdf->SetFooterMargin($size);

$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->SetFont('helvetica', '', 8);

$pdf->AddPage();

$style = array( 
	'position' => 'C',
	'border' => false, 
	'text' => false,
);

$pdf->setXY(7,15);

$params = $pdf->serializeTCPDFtagParameters(array($stock_lotno, 'C39E', '', '', '', 18, 0.4, $style, 'N'));
$str='<table cellspacing="1" cellpadding="5" border="0"><tr><td align="center" style="text-align: center;">';
$str .= '<tcpdf method="write1DBarcode" params="'.$params.'" />';
$str .='</td></tr></table>';

$pdf->writeHTML($str, true, 0, true, 0);

$pdf->setXY(7,38);

$stock_lotno = trim(chunk_split($stock_lotno, 1, ' '));
$barcodeTitle = '<table cellspacing="1" cellpadding="5"  style="text-align: center; font-size: 10px"><tr><td><b>'.$stock_lotno.'</b></td></tr></table>';

$pdf->writeHTML($barcodeTitle, true, 0, true, 0);
$pdf->Ln();

$pdf->Output('example_027.pdf', 'I');
