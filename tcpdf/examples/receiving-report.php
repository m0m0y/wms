<?php

require_once "tcpdf_include.php";
require_once "../../controller/controller.db.php";
require_once "../../controller/controller.sanitizer.php";
require_once "../../model/model.receiving.php";

$receiving = new Receiving();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('Receiving Report');
$pdf->SetSubject('Receiving report');

$pdf->SetPrintFooter(true);
$pdf->SetPrintHeader(false);

$margin = 15;

$pdf->SetMargins($margin, $margin, $margin);
$pdf->SetHeaderMargin($margin);
$pdf->SetFooterMargin($margin);

$pdf->SetAutoPageBreak(TRUE, $margin);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('dejavusans', '', 9);


$report_id = Sanitizer::filter("report", 'get', 'int');

$report = $receiving->getReport($report_id);

if(!$report) {
	echo "Receiving Report Not Found";
	exit;
}

/* echo "<pre>";
print_r($report);
'.$report[0]['no_package'].'
die(); */

$html = "";

$html .= '	
	<table style="border-spacing: 0 5px" width="100%">
		<tr>
			<td width="50%" align="left"><span style="font-size: 10px;" color="#a9a9a9"><b>Company Name</b></span>:</td>
			<td width="50%" align="right"><span style="font-size: 10px;" color="#a9a9a9"><b>Control No.</b></span>: <span color="#ff0000"><b>#'.$report[0]['control_no'].'</b></span></td>
		</tr>
		<tr>
			<td width="50%" align="left">'.$report[0]['company_name'].'</td>
			<td width="50%" align="right"><span style="font-size: 10px;" color="#a9a9a9"><b>No. of packages</b></span>: &nbsp;&nbsp;&nbsp;</td>
		</tr>
		
		<tr>
			<td width="50%" align="left"><span style="font-size: 10px;" color="#a9a9a9"><b>Broker/Supplier/Origin</b></span>: '.$report[0]['origin'].'</td>
			<td width="50%" align="right"><span style="font-size: 10px;" color="#a9a9a9"><b>Ref No.</b></span>: <span color="#ff0000"><b>#'.$report[0]['reference'].'</b></span></td>
		</tr>
		<tr>
			<td width="50%" align="left"><span style="font-size: 10px;" color="#a9a9a9"><b>Date of Delivery</b></span>: '.
			
				date_format(date_create($report[0]['delivery']), 'M d, D Y').'</td>
			<td width="50%" align="right">'.$report[0]['type'].'  &mdash;  '.$report[0]['kind'].'  &mdash;  '.$report[0]['expected_weight'].'</td>
		</tr>
	</table>
	';
		
	/* Report table head  */
	$html .= '
		<br><br><br>
		<table cellpadding="5" width="100%" style="border-spacing: 0;">
			<tr align="center" style="font-size:10px;background-color: #f2f2f2" color="#a9a9a9">
				<th style="border: 1px solid #e2e2e2">ITEM CODE</th>
				<th style="border: 1px solid #e2e2e2">DESCRIPTION</th>
				<th style="border: 1px solid #e2e2e2">LOT NO</th>
				<th style="border: 1px solid #e2e2e2">EXP DATE</th>
				<th style="border: 1px solid #e2e2e2">QTY RECEIVED</th>
				<th style="border: 1px solid #e2e2e2">UNIT</th>
				<th style="border: 1px solid #e2e2e2">USER ACCOUNT</th>
			</tr>';
			
		
	foreach($report[0]['items'] as $key=>$value) {

		$even = ($key % 2 == 0) ? '' : 'background-color: #f2f2f2';
		
		$discrepancy = ($report[0]["items"][$key]["item_received"] < $report[0]["items"][$key]["item_expected"])
		? (int)$report[0]["items"][$key]["item_expected"] - (int)$report[0]["items"][$key]["item_received"]
		: 0;

		$html .= '
			<tr align="center" style="font-size:10px; '.$even.'">
				<th style="border: 1px solid #e2e2e2">'.$report[0]["items"][$key]["item_code"].'</th>
				<th style="border: 1px solid #e2e2e2">'.$report[0]["items"][$key]["item_description"].'</th>
				<th style="border: 1px solid #e2e2e2">'.$report[0]["items"][$key]["item_lot"].'</th>
				<th style="border: 1px solid #e2e2e2">'. ucfirst($report[0]["items"][$key]["item_expiry_month"]) . ' &mdash; ' . $report[0]["items"][$key]["item_expiry_year"] .'</th>
				<th style="border: 1px solid #e2e2e2">'.$report[0]["items"][$key]["item_received"].'</th>
				<th style="border: 1px solid #e2e2e2">'.$report[0]["items"][$key]["item_unit"].'</th>
				<th style="border: 1px solid #e2e2e2">'.$report[0]["items"][$key]["user_fullname"].'</th>
			</tr>';					  	
	}	
					  

 	$html .= '</table>';

	$html .= '	
	<br><br><br>

	<table style="border-spacing: 0 5px" width="100%">
		<tr>
			<td width="50%" align="left"><span style="font-size: 10px;" color="#a9a9a9"><b>Remarks</b></span>:</td>
			<td width="50%" align="right"><span style="font-size: 10px;" color="#a9a9a9"><b>Disposition in case of emergency</b></span></td>
		</tr>
		<tr>
			<td width="50%" align="left">'.$report[0]['remarks'].'</td>
			<td width="50%" align="right">'.$report[0]['disposition'].'</td>
		</tr>
	</table>
	';

		/* display logo */
	$pdf->AddPage('L', 'A4');
	$pdf->Image('../../static/logo.png', 15, 15, '', 12, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
	
	$meta = '
		<table align="right" width="100%" style="border-spacing: 0 5px">
			<tr>
				<td style="width:100%"><h4><b>Receiving Report</b></h4></td>
			</tr>
			<tr>
				<td>'.date('M d, Y ').'</td>
			</tr>
		</table>';

	$pdf->writeHTML($meta, true, false, true, false, '');
	
	
	$pdf->SetXY(15,34);
	$pdf->writeHTML($html, true, false, true, false, '');
	ob_end_clean();
	$pdf->Output('stockcard.pdf', 'I');



?>