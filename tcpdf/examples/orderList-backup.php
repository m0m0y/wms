<?php

require_once('tcpdf_include.php');
$slip_id = $_GET['slip_id'];
$box_no = $_GET['box_no'];
require_once "../../controller/controller.db.php";
require_once "../../model/model.packing.php";
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

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
$pdf->SetFont('Courier', '', 11);

// add a page
$pdf->AddPage('P','LETTER');
$pdf->setJPEGQuality(75);
// Image example with resizing

$pdf->Image('../../static/logo.png', 15, 10,70, 18, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);

$pdf->Image('../../static/truck.png', 155, 45,50, 23, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);

// print a message
$txt = "";
$pdf->MultiCell(70, 50, $txt, 0, 'J', false, 1, 125, 30, true, 0, false, true, 0, 'T', false);
$pdf->SetY(45);
$pdf->setCellPaddings( $left = '', $top = '2', $right = '', $bottom = '2');
// -----------------------------------------------------------------------------
$packing = new Packing();

$order = $packing->getAllOrders();

foreach($order as $k=>$v) {

$html = '<table border="0px" style="width:100%">
			<tr>
				<td align="center" style="width:75%;color:white;background-color:#0066cc"><h1 style="font-size:34px;width:100%">ORDER LIST</h1></td>
				<td style="width:25%"><label style="font-size:14px"></label></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td align="center" style="width:50%;color:white;background-color:#0066cc"><label style="font-size:16px;width:100%">CUSTOMER INFORMATION</label></td>
				<td style="width:50%"><label style="font-size:14px"></label></td>
			</tr>
			<br>
			<tr align="left">
				<td style="width:5%"></td>
				<td style="width:12%"><b>Customer:</b></td>
				<td style="width:38%">'.ucfirst($v['ship_to']).'</td>
				<td style="width:15%"><b>Slip No:</b></td>
				<td style="width:30%">'.$v['slip_no'].'</td>
			</tr>
			<tr align="left">
				<td style="width:5%"></td>
				<td style="width:12%"><b>Address:</b></td>
				<td style="width:38%">'.$v['customer_address'].'</td>
				<td style="width:15%"><b>Ship Date:</b></td>
				<td style="width:30%">'.date('F d, Y',strtotime($v['ship_date'])).'</td>
			</tr>
			<tr align="left">
				<td style="width:5%"></td>
				<td style="width:12%"><b>Ship To:</b></td>
				<td style="width:38%">'.$v['customer_address'].'</td>
				<td style="width:15%"><b>Box Number:</b></td>
				<td style="width:30%">'.$box_no.'</td>
			</tr>
			<br>
			<tr>
				<td align="center" style="width:50%;color:white;background-color:#0066cc"><label style="font-size:16px;width:100%">ORDER DETAILS</label></td>
				<td style="width:50%"><label style="font-size:14px"></label></td>
			</tr>
			<br>
			<tr align="center" style="font-size:14px;font-weight:bold">
				<td style="width:12%"></td>
				<td style="width:20%;background-color:#e6f9ff;border:5px solid white">Item Code</td>
				<td style="width:50%;background-color:#e6f9ff;border:5px solid white">Item Description</td>
				<td style="width:18%;background-color:#e6f9ff;border:5px solid white">Quantity</td>
			</tr>';
			$a = 1;
			$order_details = $packing->getAllOrdersdetailsReport($slip_id,$box_no);
			foreach ($order_details as $k => $x) {
$html .=   '<tr align="center" style="font-size:12px">
				<td style="width:12%;background-color:#e6f9ff;border:5px solid white">'.$a.'</td>
				<td style="width:20%">'.$x['product_code'].'</td>
				<td style="width:50%">'.$x['product_description'].'</td>
				<td style="width:18%">'.$x['quantity_shipped'].'</td>
			</tr>';
			$a++;
			}
$html .= '</table>';


// $html .= '<br><br><br><table style="width:100%">
// 			<tr>
// 				<td style="width:5%"></td>
// 				<td style="width:16%"><b>Received by:</b></td>
// 				<td style="width:34%">__________________________</td>
// 				<td style="width:45%"></td>
// 			</tr>
// 			<br><br>
// 			<tr>
// 				<td style="width:5%"></td>
// 				<td style="width:16%"><b>Signature:</b></td>
// 				<td style="width:34%"></td>
// 				<td style="width:45%"></td>
// 			</tr>
// 			<tr>
// 				<td style="width:5%"></td>
// 				<td style="width:16%"></td>
// 				<td style="width:34%">__________________________</td>
// 				<td style="width:45%"></td>
// 			</tr>
// 		</table>';
}
// Print text using writeHTMLCell()
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('shipping_label.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
