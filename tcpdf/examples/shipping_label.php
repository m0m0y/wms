<?php

$slip_no = (isset($_GET['a'])) ?$_GET['a'] : 'x';
$ship_to = (isset($_GET['b'])) ? $_GET['b'] : 'x';
$customer_address = (isset($_GET['c'])) ? $_GET['c'] : 'x';
$remarks = (isset($_GET['d'])) ? $_GET['d'] : '';
$courier = (isset($_GET['e'])) ? $_GET['e'] : 'x';
$slip_id = (isset($_GET['g'])) ? $_GET['g'] : 'x';

$weight_kg = (isset($_GET['w'])) ? $_GET['w'] : 'x';
$weightPerbox = explode(",",$weight_kg);

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

foreach($box_page as $k=>$vv) { $page++; }

$box = $packing->getAllBoxes($slip_id);
$b = 0;
foreach($box as $k=>$v) {
	
	$pdf->AddPage();

	$bMargin = $pdf->getBreakMargin();
	$auto_page_break = $pdf->getAutoPageBreak();
	$pdf->SetAutoPageBreak(false, 0);
	$img_file = '../../static/shipping-label/label_lg.png';
	$pdf->Image($img_file, 3, 3, 95.6, 146.4, '', '', '', false, 100, '', false, false, 0);
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	$pdf->setPageMark();

	$pdf->Image('../../static/logo-bnw.png', 7, 10.5, 35, 0, 'PNG', '', '', true, 1000, '', false, false, 0, false, false, false);

	$responsiveFont = 13;
	$headText = 13;

	ob_start();
	?>
	<table width="160">
		<tr style="font-size: <?= $responsiveFont ?>px; text-align: center;">
			<td><b><span style="font-size: 30px;"> <?= $a . " <span style=\"font-size: 15px\">of</span> " . $page ?></b></span></td>
		</tr>
	</table>
	<?php
	$box_total = ob_get_clean();
	
	ob_start();
	?>
	<table width="138">
		<tr style="font-size: <?= $responsiveFont ?>px; text-align: center;">
			<td><b><span style="font-size: 10px;">Of.</span><span style="font-size: 30px;"> <?= $page . "</span>" ?></b></td>
		</tr>
	</table>
	<?php
	$box_count = ob_get_clean();

	ob_start();
	?>
	<table width="85">
		<tr style="font-size: 20px; text-align: center;">
			<td><b><?= $weightPerbox[$b] ?></b></td>
		</tr>
	</table>
	<?php
	$weight = ob_get_clean();

	
	ob_start();
	?>
	<table width="60">
		<tr style="text-align: center;">
			<td><span style="font-size: 7px;">KILOGRAMS</span></td>
		</tr>
	</table>
	<?php
	$weight_description = ob_get_clean();

		
	ob_start();
	?>
	<table width="138">
		<tr style="text-align: center;">
			<td><span style="font-size: <?= $responsiveFont ?>px; color: #fff">Package<?php if((int)$page > 1) { echo 's'; } ?></span></td>
		</tr>
	</table>
	<?php
	$pack_description = ob_get_clean();
	
	ob_start();
	?>
	<table width="60">
		<tr style="text-align: center">
			<td><span style="font-size: 7px;">WEIGHT</span></td>
		</tr>
	</table>
	<?php
	$weight_label = ob_get_clean();


	ob_start();
	?>
	<table>
		<tr style="font-size: 7px">
			<td>ORDER REFERENCE</td>
		</tr>
		<tr style="font-size: 18px">
			<td><b><?= $po_no ?></b></td>
		</tr>
	</table>
	<?php
	$po = ob_get_clean();
	

	ob_start();
	?>
	<table style="padding-right: 8px;">
		<tr style="font-size: 10px; text-align: right;">
			<td>Inmed Corporation<br>5 Calle Industria Bagumbayan<br>Quezon City 1110 Philippines</td>
		</tr>
	</table>
	<?php
	$from_address_top = ob_get_clean();

	ob_start();
	?>
	<table style="padding-right: 8px;">
		<tr style="font-size: 7px; text-align: right;">
			<td>OUR REFERENCE</td>
		</tr>
		<tr style="font-size: 18px; text-align: right;">
			<td><b><?= $slip_no ?></b></td>
		</tr>
	</table>
	<?php
	$slip = ob_get_clean();

	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b>Order Date</b></td>
		</tr>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><?= date_format(date_create($order_date), 'F jS Y') ?></td>
		</tr>
	</table>
	<?php
	$date_order = ob_get_clean();

	
	ob_start();
	?>
	<table>
		<tr style="font-size: 5px; text-align: center">
			<td>inmed.com.ph</td>
		</tr>
	</table>
	<?php
	$website = ob_get_clean();

	
	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b>Print Date</b></td>
		</tr>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><?= date('F jS Y') ?></td>
		</tr>
	</table>
	<?php
	$print_date = ob_get_clean();

	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><?= $courier ?></td>
		</tr>
	</table>
	<?php
	$ship_via = ob_get_clean();

	
	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px;">
			<td><b>Ship via</b></td>
		</tr>
	</table>
	<?php
	$ship_via_text = ob_get_clean();
	
	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px; text-align: left;">
			<td><b>Buyer</b></td>
		</tr>
	</table>
	<?php
	$o_to = ob_get_clean();


	ob_start();
	?>
	<table width="120">
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

	ob_start();
	?>
	<table>
		<tr style="font-size: <?= $responsiveFont ?>px">
			<td><b><?= ucwords(strtolower($ship_to)) ?></b></td>
		</tr>
		<tr style="font-size: <?= $responsiveFont ?>px">
			<td><?= ucfirst(strtolower($customer_address)) ?></td>
		</tr>
	</table>
	<?php $c_add = ob_get_clean();
	
	/* $pdf->SetXY(7, 78);
	$pdf->writeHTML($box); */

	$pdf->SetXY(16, 65);
	$pdf->writeHTML($c_add);

	$pdf->SetXY(10, 25.5);
	$pdf->writeHTML($po);
	
	$pdf->SetXY(53, 25.5);
	$pdf->writeHTML($slip);
	
	$pdf->StartTransform();
	$pdf->Rotate(90, 12, 77);
	$pdf->SetXY(11, 71);
	$pdf->writeHTML($o_to);
	$pdf->StopTransform();

	/*  */

	$pdf->SetXY(60, 85.5);
	$pdf->writeHTML($date_order);

	$pdf->SetXY(60, 96);
	$pdf->writeHTML($print_date);

	
	$pdf->StartTransform();
	$pdf->Rotate(90, 12.5, 116.5);
	$pdf->writeHTML($ship_via_text);
	$pdf->StopTransform();

	
	$pdf->SetXY(16, 113);
	$pdf->writeHTML($ship_via);

	
	$pdf->SetXY(55, 128.5);
	$pdf->writeHTML($weight_label);

	$pdf->SetXY(51.5, 132.5);
	$pdf->writeHTML($weight);

	$pdf->SetXY(55, 141.5);
	$pdf->writeHTML($weight_description);
	
	$pdf->SetXY(40, 10);
	$pdf->writeHTML($from_address_top);

	$pdf->StartTransform();
	$pdf->Rotate(90, 19.5, 140.5);
	$pdf->SetXY(16, 127);
	$pdf->writeHTML($remarked_text);
	$pdf->StopTransform();

	$pdf->SetXY(16, 131);
	$pdf->writeHTML($remarked);
	
	$style = array(
		'position' => 'C',
		'border' => false,
		'fgcolor' => array(0,0,0),
		'text' => true,
	);

	$pdf->write1DBarcode($v['box_number'], 'C39E', 7, 40, '', 15, 0.34, $style, 'N');

	$style = array(
		'border' => false,
		'vpadding' => '0',
		'hpadding' => '0',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false,
		'module_width' => 1,
		'module_height' => 1
	);
	
	$pdf->SetXY(3.5, 93);
	$pdf->writeHTML($box_total);

	$pdf->SetXY(8, 84.5);
	$pdf->writeHTML($pack_description);
	
	$pdf->write2DBarcode('https://inmed.com.ph', 'QRCODE,H', 78, 127, 15, 15, $style, 'N');

	
	$pdf->SetXY(76, 143);
	$pdf->writeHTML($website);
	
	$pdf->Ln();
	$a++;
	$b++;
}

$pdf->Output('shipping_label.pdf', 'I');


