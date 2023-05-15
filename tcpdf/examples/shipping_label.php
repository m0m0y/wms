<?php

$slip_no = (isset($_GET['a'])) ?$_GET['a'] : 'x';
$ship_to = (isset($_GET['b'])) ? $_GET['b'] : 'x';
$billto = (isset($_GET['c'])) ? $_GET['c'] : 'x';
$remarks = (isset($_GET['d'])) ? $_GET['d'] : '';
$courier = (isset($_GET['e'])) ? $_GET['e'] : 'x';
$package = (isset($_GET['f'])) ? $_GET['f'] : 'x';
$slip_id = (isset($_GET['g'])) ? $_GET['g'] : 'x';

// $weight_kg = (isset($_GET['w'])) ? $_GET['w'] : 'x';
// $weightPerbox = explode(",",$weight_kg);
$invoice_num = (isset($_GET['h'])) ? $_GET['h'] : 'x';

require_once "tcpdf_include.php";
require_once "../../controller/controller.db.php";
require_once "../../model/model.packing.php";

// 4 x 2.5 in 101.6 x 63.5
$packing = new Packing();

$order_ = $packing->getOrder($slip_no);

$po_no = $order_[0]["po_no"];
$order_date = $order_[0]["slip_order_date"];

$thermalSize = array(101.6, 152.4);

$pdf = new TCPDF('P', 'mm', $thermalSize, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('Shipping Label');
$pdf->SetSubject('Shipping Label');

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
$a = 1;
$page = 0;

$box_page = $packing->getAllBoxes($slip_id);

if($courier == "Lalamove") {
    $letter = "PL";
} else if ($courier == "Grab") {
    $letter = "PG";
} else if ($courier == "Pickup") {
    $letter = "PC";
} else if ($courier == "Lex PH") {
    $letter = "PX";
} else if ($courier == "Van") {
    $letter = "PV";
} else if ($courier == "Transportify") {
    $letter = "PT";
} else if ($courier == "Sea") {
    $letter = "PS";
} else if ($courier == "Air") {
    $letter = "PA";
} else if ($courier == "LBC") {
    $letter = "PL";
}

// foreach($box_page as $k=>$vv) { $page++; }
for ($page = 0; $page <= $package; $page++) { $pages = $page; }

$box = $packing->getAllBoxes($slip_id);
$b = 0;

foreach($box as $k=>$v) { $box_number = $v['box_number']; }

// foreach($box as $k=>$v) {
for ($x = 1; $x <= $package; $x++) {
	$pdf->AddPage();

	$bMargin = $pdf->getBreakMargin();
	$auto_page_break = $pdf->getAutoPageBreak();
	$pdf->SetAutoPageBreak(false, 0);
	$img_file = '../../static/shipping-label/label_lg1.png';
	$pdf->Image($img_file, 3, 3, 95.6, 146.4, '', '', '', false, 100, '', false, false, 0);
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	$pdf->setPageMark();

	$pdf->Image('../../static/panamed-bnw.png', 15, 9, 42, 0, 'PNG', '', '', true, 1000, '', false, false, 0, false, false, false);
    $pdf->Image('../../static/shipping-label/R2.png', 7, 129, 31, 0, 'PNG', '', '', true, 1000, '', false, false, 0, false, false, false);

	$responsiveFont = 13;
	$headText = 13;

	ob_start();
	?>
	<table width="100">
		<tr style="font-size: <?= $responsiveFont ?>px; text-align: center;">
		<td><b><span style="font-size: 60px;"><?= $letter ?></b></span></td>
		</tr>
	</table>
	<?php
	$letters = ob_get_clean();

	ob_start();
	?>
    <table>
		<tr style="font-size: 9px; text-align: left;">
		<td>488 G. Araneta Avenue corner Del Monte Avenue<br>Brgy. Sienna, Quezon City 1114 Philippines<br><small>Email: info@panamed.com.ph</small></td>
		</tr>
	</table>
	<?php
	$from_address_top = ob_get_clean();

	ob_start();
	?>
    <table>
		<tr style="font-size: 7px">
			<td>ORDER REFERENCE</td>
		</tr>
		<tr style="font-size: 16px">
			<td><b><?= $po_no ?></b></td>
		</tr>
	</table>
	<?php
	$po = ob_get_clean();
	
	ob_start();
	?>
		<table width="100">
		<tr style="font-size: 7px;">
			<td>INVOICE NUMBER</td>
		</tr>
		<tr style="font-size: 16px;">
			<td><b><?= $invoice_num ?></b></td>
		</tr>
	</table>
	<?php
	$invoice = ob_get_clean();

	ob_start();
	?>
	<table>
        <tr style="font-size: 7px;">
			<td>SHIP VIA</td>
		</tr>
	</table>
	<?php
	$ship_via_text = ob_get_clean();
	
	ob_start();
	?>
    <table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b><?= $courier ?></b></td>
		</tr>
	</table>
	<?php
	$ship_via = ob_get_clean();
	
	ob_start();
	?>
	<table>
		<tr style="font-size: 11px;">
			<td>Order Date:</td>
		</tr>
	</table>
	<?php
	$date_order_text = ob_get_clean();
	
	ob_start();
	?>
    <table>
        <tr style="font-size: <?= $responsiveFont ?>px;">
			<td style="text-align: center;"><?= date_format(date_create($order_date), 'F jS Y') ?></td>
		</tr>
    </table>
    <?php
	$date_order = ob_get_clean();
	
	ob_start();
	?>
	<table>
		<tr style="font-size: 11px;">
			<td>Print Date:</td>
		</tr>
	</table>
    <?php
        $print_date_text = ob_get_clean();

        ob_start();
    ?>
    <table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td style="text-align: center;"><?= date('F jS Y') ?></td>
		</tr>
	</table>
	<?php
	$print_date = ob_get_clean();

	ob_start();
	?>
    <table width="100">
		<tr>
			<td><span style="font-size: <?= $responsiveFont ?>px;">Package<?php if((int)$page > 1) { echo 's'; } ?>:</span></td>
		</tr>
	</table>
	<?php
	$pack_description = ob_get_clean();
	
	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b><span style="font-size: 30px;"> <?= $a . " <span style=\"font-size: 15px\">of</span> " . $pages ?></b></span></td>
		</tr>
	</table>
	<?php
	$box_total = ob_get_clean();
	
	ob_start();
	?>
    <table>
		<tr style="font-size: 10px; text-align: left;">
			<td style="color: white;">Ship To</td>
		</tr>
	</table>
	<?php
	$o_to = ob_get_clean();

	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><?= $billto ?></td>
		</tr>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b><?= $ship_to ?></b></td>
		</tr>
	</table>
	<?php 
    $c_add = ob_get_clean();

    ob_start();
	?>
	
	<table width="170">
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><?= ($remarks) ? : 'Please handle package with care.' ?></td>
		</tr>
	</table>
	<?php
	$remarked = ob_get_clean();

	ob_start();
	?>
	<table width="120">
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b>Remarks</b></td>
		</tr>
	</table>
	<?php
	$remarked_text = ob_get_clean();

	$pdf->SetXY(68, 8);
	$pdf->writeHTML($letters);

	$pdf->SetXY(10, 21);
	$pdf->writeHTML($from_address_top);

    $pdf->SetXY(7, 33.5);
	$pdf->writeHTML($po);

    $pdf->SetXY(35.5, 33.5);
	$pdf->writeHTML($invoice);

	$pdf->SetXY(70, 34.5);
	$pdf->writeHTML($ship_via_text);
	
	$pdf->SetXY(70, 36.5);
	$pdf->writeHTML($ship_via);

    $style = array(
		'position' => 'C',
		'border' => false,
		'fgcolor' => array(0,0,0),
		'text' => true,
	);

	$pdf->write1DBarcode($box_number, 'C39E', 7, 51, '', 20, 0.35, $style, 'N');

	$pdf->SetXY(53, 105);
	$pdf->writeHTML($date_order_text);

    $pdf->SetXY(45, 109);
	$pdf->writeHTML($date_order);

	$pdf->SetXY(53, 114);
	$pdf->writeHTML($print_date_text);

	$pdf->SetXY(45, 117);
	$pdf->writeHTML($print_date);

	$pdf->SetXY(8, 105);
	$pdf->writeHTML($pack_description);

	$pdf->SetXY(13.5, 109);
	$pdf->writeHTML($box_total);

	$pdf->StartTransform();
    $pdf->Rotate(90, 13.5, 80);
	$pdf->SetXY(0, 73.5);
	$pdf->writeHTML($o_to);
	$pdf->StopTransform();

	$pdf->SetXY(15, 83.5);
	$pdf->writeHTML($c_add);
	
	$pdf->SetXY(42, 128);
	$pdf->writeHTML($remarked_text);

	$pdf->SetXY(42, 133);
	$pdf->writeHTML($remarked);
    
	/*  */

    /* $pdf->SetXY(7, 78);
	$pdf->writeHTML($box); */
	
	// $pdf->SetXY(55, 128.5);
	// $pdf->writeHTML($weight_label);

	// $pdf->SetXY(51.5, 132.5);
	// $pdf->writeHTML($weight);

	// $pdf->SetXY(55, 141.5);
	// $pdf->writeHTML($weight_description);
	
	
	$pdf->Ln();
	$a++;
	$b++;
}

ob_end_clean();
$pdf->Output('shipping_label.pdf', 'I');
