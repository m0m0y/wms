<?php

require_once "tcpdf_include.php";
require_once "../../controller/controller.db.php";
require_once "../../model/model.packing.php";

$thermalSize = array(101.6, 152.4);

$pdf = new TCPDF('P', 'mm', $thermalSize, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('System Generated Barcode');
$pdf->SetSubject('System Generated Barcode');

$pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, '', '');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$size = 6;
$pdf->SetMargins($size, $size, $size);
$pdf->SetHeaderMargin($size);
$pdf->SetFooterMargin($size);

$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('Helvetica', '', 11);


$stock_lotno = (isset($_GET['stock_lotno']) && !empty($_GET['stock_lotno'])) ? $_GET['stock_lotno'] : '404-NOTFOUND';
$lotno = explode("**",$stock_lotno);

$lotno[0] = ($lotno[0] < 10) ? "0".$lotno[0] : $lotno[0];
	
$pdf->AddPage();

$bMargin = $pdf->getBreakMargin();
$auto_page_break = $pdf->getAutoPageBreak();
$pdf->SetAutoPageBreak(false, 0);
$img_file = '../../static/shipping-label/label_lg_b.png';
$pdf->Image($img_file, 3, 3, 95.6, 146.4, '', '', '', false, 100, '', false, false, 0);
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
$pdf->setPageMark();

// $style = array(
// 	'position' => 'C',
// 	'border' => false,
// 	'fgcolor' => array(0,0,0),
// 	'text' => false,
// );


// $pdf->write1DBarcode($lotno[0], 'C39E', 7, 40, '', 20, 0.34, $style, 'C');

$style = array( 
	'position' => 'C',
	'border' => false, 
	'text' => false,
);

// $params = $pdf->serializeTCPDFtagParameters(array($lotno[0], 'C39E', '', '', '', 20, 1.4, $style, 'N'));    
// $str ='<table width="95%" border="0"><tr><td align="center" style="text-align: center;">';
// $str .= '<tcpdf method="write1DBarcode"params="'.$params.'" />';
// $str .='</td></tr></table>';

// $pdf->setXY(10,55);
// $pdf->writeHTML($str);

// $str = '<label style="font-size:28px; text-align:center; font-weight:bold;">'.$lotno[1].'</label>';

// $pdf->setXY(5,43);
// $pdf->writeHTML($str);

/* Vertical */

$params = $pdf->serializeTCPDFtagParameters(array($lotno[0], 'C39E', '', '', '', 20, 1.4, $style, 'N'));    
$str ='<table width="75%" border="0"><tr><td align="center" style="text-align: center;">';
$str .= '<tcpdf method="write1DBarcode"params="'.$params.'" />';
$str .='</td></tr></table>';

$pdf->StartTransform();
$pdf->setXY(50,77);
$pdf->Rotate(90);
$pdf->writeHTML($str);
$pdf->StopTransform();

$str = '<label style="font-size:40px; text-align:center; font-weight:bold;">A1001</label>';

$pdf->StartTransform();
$pdf->setXY(18, 98);
$pdf->Rotate(90);
$pdf->writeHTML($str);
$pdf->StopTransform();

$str = '<label style="font-size:40px; text-align:center; font-weight:bold;">A-01</label>';

$pdf->StartTransform();
$pdf->setXY(32, 95);
$pdf->Rotate(90);
$pdf->writeHTML($str);
$pdf->StopTransform();

$pdf->Output('shipping_label.pdf', 'I');


