<?php

$stock_lotno = (isset($_GET['stock_lotno']) && !empty($_GET['stock_lotno'])) ? $_GET['stock_lotno'] : '404-NOTFOUND';

require_once('tcpdf_include.php');

// 3.5 x 2.2 in
 
// $thermalSize = array(101.6, 63.5);
$thermalSize = array(75, 40);

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

$pdf->setXY(7,2);

$lotno = explode("**",$stock_lotno);

$lotno[0] = ($lotno[0] < 10) ? "0".$lotno[0] : $lotno[0];
$params = $pdf->serializeTCPDFtagParameters(array($lotno[0], 'C39E', '', '', '', 17, 1.4, $style, 'N'));    
$str = '<br><br><label style="font-size:20px;text-align:center;font-weight:bold;">'.$lotno[1].'</label><br>';
$str .='<table cellspacing="1" cellpadding="5" border="0"><tr><td align="center" style="text-align: center;">';
$str .= '<tcpdf method="write1DBarcode" params="'.$params.'" />';
$str .='</td></tr></table>';

$pdf->writeHTML($str, true, 0, true, 0);

$pdf->Output('example_027.pdf', 'I');
