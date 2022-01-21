<?php

$_GET['action'] = "pdf";
$product_id = $_GET['product_id'];
require_once('tcpdf_include.php');
require_once "../../controller/controller.db.php";
require_once "../../model/model.report.php";

$stockcard = new Stockcard();


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('Stock Card Total Report');
$pdf->SetSubject('Stock Card Total Report');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

$margin = 15;

$pdf->SetMargins($margin, $margin, $margin);
$pdf->SetHeaderMargin($margin);
$pdf->SetFooterMargin($margin);


$pdf->SetAutoPageBreak(TRUE, $margin);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetFont('dejavusans', '', 9);

$html = '<br>';

$product_header = $stockcard->getProductDetails($product_id);
$html .= '
		<table cellpadding="3" width="100%">
			<tr>
				<td>'.$product_header['product_description'].'</td>
				<td align="right">'.$product_header['product_code'].'</td>
			</tr>
		</table>';
		
$html .= '
	<br><br>
	<table cellpadding="5" width="100%" style="border-spacing: 0;">
		<tr align="center" style="font-size:10px;background-color: #f2f2f2" color="#a9a9a9">
			<th style="border: 1px solid #e2e2e2">LN</th>
			<th style="border: 1px solid #e2e2e2">UoM</th>
			<th style="border: 1px solid #e2e2e2">EXP DATE</th>
			<th style="border: 1px solid #e2e2e2">BALANCE</th>
		</tr>';

$total = 0;
$lots = $stockcard->getAllLots($product_id);

if(empty($lots)){
	$html .= '
	<tr align="center" style="font-size:10px">
		<td colspan="4" style="border: 1px solid #e2e2e2" color="#a9a9a9">No Data Available</td>																										
	</tr>';	
} else {
	foreach($lots as $k=>$v) {
		$html .= '
		<tr align="center" style="font-size:10px">
			<td style="border: 1px solid #e2e2e2">'.$v['stock_lotno'].'</td>
			<td style="border: 1px solid #e2e2e2">'.$product_header['unit'].'</td>
			<td style="border: 1px solid #e2e2e2">'.$v['stock_expiration_date'].'</td>
			<td style="border: 1px solid #e2e2e2">'.$v['stock_qty'].'</td>																											
		</tr>';	
		$total += $v['stock_qty'];
	}
}

$html .= '
	<tr align="center" style="font-size:10px">
		<td style="border-left-width:0px;border-right-width:1px;border-bottom-width:0px;"></td>
		<td style="border: 1px solid #e2e2e2"></td>
		<td style="border: 1px solid #e2e2e2" color="#a2a2a2">Total</td>
		<td style="border: 1px solid #e2e2e2">'.$total.'</td>																																	
	</tr>';
							  
$html .= '</table>';


if($_GET['action'] == "pdf"){

	$pdf->AddPage();
	$pdf->Image('../../static/logo.png', 15, 15, '', 12, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
	$meta = '
		<table align="right" width="100%" style="border-spacing: 0 5px">
			<tr>
				<td style="width:100%"><h4><b>All Lots</b></h4></td>
			</tr>
			<tr>
				<td>'.date('F d, Y ').'</td>
			</tr>
		</table>';

	$pdf->writeHTML($meta, true, false, true, false, '');

	$pdf->SetXY(15,34);
	$pdf->writeHTML($html, true, false, true, false, '');
	
	ob_end_clean();
	$pdf->Output('stockcard.pdf', 'I');

}

if($_GET['action'] == "export"){
	$backup_name = "stockcard.xls";
	header('Content-Type: application/octet-stream');   
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
	echo $html;
	exit;
}


