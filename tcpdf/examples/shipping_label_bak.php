<?php

$slip_no = ($_GET['a']) ?: 'x';
$ship_to = ($_GET['b']) ?: 'x';
$customer_address = ($_GET['c']) ?: 'x';
$remarks = ($_GET['d']) ?: '';
$courier = ($_GET['e']) ?: 'x';
$slip_id = ($_GET['g']) ?: 'x';

require_once "tcpdf_include.php";
require_once "../../controller/controller.db.php";
require_once "../../model/model.packing.php";

// 4 x 2.5 in 101.6 x 63.5

$thermalSize = array(101.6, 63.5);

$pdf = new TCPDF('l', 'mm', $thermalSize, true, 'UTF-8', false);


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
$size = 8;
$pdf->SetMargins($size, $size, $size);
$pdf->SetHeaderMargin($size);
$pdf->SetFooterMargin($size);

$pdf->SetAutoPageBreak(TRUE, 0);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->setCellPadding(2);

$pdf->SetFont('Helvetica', '', 11);
$a = 1;
$page = 0;
$packing = new Packing();
$box_page = $packing->getAllBoxes($slip_id);
foreach($box_page as $k=>$vv) {
	$page++;
}
$box = $packing->getAllBoxes($slip_id);
foreach($box as $k=>$v) {
	
	$pdf->AddPage();

	$pdf->Image('../../static/logo-sm.png', 8, 7, 30, 0, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);

	ob_start(); ?>
	<b style="font-size: 13px;"><?= $a." of ".$page ?></b>
	<?php $html = ob_get_clean();
	$pdf->SetXY(82, 5);
	$pdf->writeHTML($html);
	ob_start(); ?>
	<table>
		<tr style="font-size: 12px;">
			<td><b>Ship To : </b> <?= $ship_to ?></td>
		</tr>
		<tr style="font-size: 12px;">
			<td><b>Ship Via : </b> <?= $courier ?></td>
		</tr>
	</table>
	<?php $html = ob_get_clean();
	$pdf->SetXY(8, 18);
	$pdf->writeHTML($html);
	ob_start(); ?>
	<table>
		<tr style="font-size: 12px;">
			<td><b>Address :</b> <?= $customer_address ?></td>
		</tr>
		<?php if(isset($remarks) && !empty($remarks)) { ?>
		<tr style="font-size: 12px;">
			<td><b>Remarks :</b> <?= $remarks ?></td>
		</tr>
		<?php } ?>
	</table>
	<?php $html = ob_get_clean();

	$pdf->SetXY(8, 27);
	$pdf->writeHTML($html);

	$style = array(
		'position' => 'L',
		'border' => false,
		'fgcolor' => array(0,0,0),
		'text' => false,
	);

	$pdf->write1DBarcode($v['box_number'], 'C39E', 15, 43, '', 12, 0.34, $style, 'N');
	$pdf->write1DBarcode($v['box_number'], 'C39E', 15, 45, '', 12, 0.34, $style, 'N');

	$pdf->Ln();

	$a++;
}

$pdf->Output('shipping_label.pdf', 'I');


