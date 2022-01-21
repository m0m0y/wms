<?php

require_once "../../controller/controller.db.php";
require_once "../../model/model.packing.php";
require_once "tcpdf_include.php";


$slip_id = $_GET['slip_id'];
$box_no = $_GET['box_no'];

$packing = new Packing();
$order = $packing->getAllOrders();


$order_details = $packing->getAllOrdersdetailsReport($slip_id,$box_no);
$a = 0;
$hThermal = 80 + (count($order_details) * 9);

// 3.5 x 2.2 in
 
$thermalSize = array(60, 210);

$pdf = new TCPDF('H', 'mm', $thermalSize, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('Box List');
$pdf->SetSubject('Box contents');

$pdf->SetPrintFooter(false);
$pdf->SetPrintHeader(false);

$size = 2;
$pdf->SetMargins($size, $size, $size);
$pdf->SetHeaderMargin($size);
$pdf->SetFooterMargin($size);

$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->SetFont('helvetica', '', 8);

$pdf->AddPage();

$pdf->setJPEGQuality(100);



$pdf->Image('../../static/logo-bnw.png', 8, 1, '', 10, 'PNG', '', '', true, 900, '', false, false, 1, false, false, false);

$pdf->setXY(3, 16);

$address = '
<table border="0" width="100%">
	<tr>
		<td style="font-size: 11px">200 C. Raymundo Avenue Caniogan,Pasig City 1606 Philippines.</td>
	</tr>
</table>';

$pdf->writeHTML($address, true, 0, true, 0);
$pdf->Ln();

foreach($order as $k=>$v) { 

    $customer = '

        <table border="0" width="100%" style="font-size: 11px">
            <tr>
                <td><b>Box No:</b></td>
                <td style="text-align: right;"><b>'.$box_no.'</b></td>
            </tr>
            <tr>
                <td><b>Slip No:</b></td>
                <td style="text-align: right;"><b>#'.$v['slip_no'].'</b></td>
			</tr>
            <tr>
                <td><b>Date:</b></td>
                <td style="text-align: right;"><b>'.date('M d Y').'</b></td>
			</tr>
        </table>
        <table border="0" width="100%" style="font-size: 11px">
            <tr><td></td></tr>
            <tr>
                <td><b>Shipping to:</b></td>
            </tr>
            <tr>
				<td>'.ucwords(strtolower($v['ship_to'])).'</td>

			</tr>
        </table>
    ';


    $pdf->setXY(2, 35);
    $pdf->writeHTML($customer, true, false, true, false, '');

    $pdf->setXY(2,75);


    $itemHead = '
    <table style="width: 100%; border="0" font-weight: bold;">
        <tr style="font-size: 11px;">
            <td style="width: 80%">Description</td>
            <td style="width: 20%" align="right">Qty</td>
        </tr>
    </table>';

    $itemHead .= '<table style="border-spacing: 0 1px"><tr><td></td></tr></table>';

	foreach ($order_details as $k => $x) {
		$itemHead .=
		'<table style="border-spacing: 0 3">
			<tr style="font-size: 11px;">
				<td style="width:50%;">'.$x['product_description'].'</td>
				<td style="width:50%;" align="right"><b>'.$x['stock_qty'].'</b> <span style="font-size: 8px">'.$x['unit_name'].'</span></td>
			</tr>
        </table>';
	}

    $pdf->writeHTML($itemHead, true, false, true, false, '');
    $pdf->Ln();

}


$pdf->Output('example_027.pdf', 'I');