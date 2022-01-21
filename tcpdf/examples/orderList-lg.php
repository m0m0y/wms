<?php

require_once('tcpdf_include.php');

$slip_id = $_GET['slip_id'];
$box_no = $_GET['box_no'];

require_once "../../controller/controller.db.php";
require_once "../../model/model.packing.php";

// create new PDF document

$thermalSize = array(215.9, 279.4);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('l', 'mm', $thermalSize, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Order List');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, '', '');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$margin = 15;
$pdf->SetMargins($margin, $margin, $margin);
$pdf->SetHeaderMargin($margin);
$pdf->SetFooterMargin($margin);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, $margin);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set a barcode on the page footer
$pdf->setBarcode(date('Y-m-d H:i:s'));

// set font
$pdf->SetFont('helvetica', '', 11);

// add a page
$pdf->AddPage('P','LETTER');
$pdf->setJPEGQuality(75);
// Image example with resizing

$pdf->Image('../../static/logo.png', 8.5, 15, '', 11, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);

$packing = new Packing();

$order = $packing->getAllOrders();

$pdf->SetXY(95, 15);

$address = '
<table border="0" width="200px" style="text-align: right; padding-right: 25px; border-right: 1px solid black;">
	<tr>
		<td>Inmed Corporation</td>
	</tr>
	<tr>
		<td>5 Calle Industria</td>
	</tr>
	<tr>
		<td> Bagumbayan, Quezon City</td>
	</tr>
	<tr><td>1110 Philippines</td></tr>
</table>
';



$pdf->writeHTML($address, true, false, true, false, '');
$pdf->Ln();

$pdf->SetXY(15, 15);

$address = '
<table width="100%" style="text-align: right;">
	<tr>
		<td>Globe Viber & WhatsApp</td>
	</tr>
	<tr>
		<td>0917-81-INMED(46633)</td>
	</tr>
	<tr>
		<td>wecare@inmed.com.ph</td>
	</tr>
	<tr>
		<td>+63.2.85711888</td>
	</tr>
</table>';

$pdf->writeHTML($address, true, false, true, false, '');
$pdf->Ln();


foreach($order as $k=>$v) {

	$pdf->SetXY(15, 45);

	$shipping_details = '
		<table border="0" width="99%">
			<tr>
				<td><b>Customer Address:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$v['customer_address'].'</td>
			</tr>
			<tr>
				<td><b>Date of shipment:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('F d, Y',strtotime($v['ship_date'])).'</td>
			</tr>
			<tr>
				<td><b>Box Number:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$box_no.'</td>
			</tr>
			<tr>
				<td><b>Slip Number:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#'.$v['slip_no'].'</td>
			</tr>
		</table>
	';
	
	$pdf->writeHTML($shipping_details, true, false, true, false, '');
	$pdf->Ln();

	$html = '<br><br><br>';
	$html .= '
		<table width="100%" style="font-weight: bold;" color="#a9a9a9">
			<tr style="font-size: 12px;">
				<td style="width: 20%">Product Code</td>
				<td style="width: 60%">Product Description</td>
				<td style="width: 20%;" align="right">Quantity</td>
			</tr>
			<tr><td></td><td></td><td></td></tr>
		</table>';

	$a = 1;
	$order_details = $packing->getAllOrdersdetailsReport($slip_id,$box_no);

	foreach ($order_details as $k => $x) {
		
		$a++;	
		$html .=
		'<table width="100%" style="border-spacing: 0 5px">
			<tr style="padding-bottom: 5px">
				<td style="width:20%;">'.$x['product_code'].'</td>
				<td style="width:60%;">'.$x['product_description'].'</td>
				<td style="width:20%;" align="right">'.$x['stock_qty'].' '.$x['unit_name'].'</td>
			</tr>
		</table>';
	}
}


$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln();

// -------------------------
// Barcode on bottom



// $style = array(
//     'border' => false,
//     'vpadding' => '10',
//     'hpadding' => '10',
//     'fgcolor' => array(0,0,0),
//     'bgcolor' => false, //array(255,255,255)
//     'module_width' => 1, // width of a single module in points
//     'module_height' => 1 // height of a single module in points
// );

// $pdf->write2DBarcode('https://www.pmc.ph', 'QRCODE,H', 5, 214, 50, 50, $style, 'N');
// $pdf->Ln();


// $pdf->SetXY(18, 256);
// $pdf->writeHTML("www.pmc.ph", true, false, true, false, '');
// $pdf->Ln();


$pdf->Output('shipping_label.pdf', 'I');
