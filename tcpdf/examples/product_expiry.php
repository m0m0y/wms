<?php


$dateFrom = $_GET['dateFrom'];
$dateTo = $_GET['dateTo'];
$_GET['action'] = "qqq";
require_once('tcpdf_include.php');
require_once "../../controller/controller.db.php";
require_once "../../model/model.report.php";

$stockcard = new Stockcard();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('List Of All Products That Will Expire');
$pdf->SetSubject('List Of All Products That Will Expire');

$pdf->setPrintHeader(false);
$margin = 15;

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->SetMargins($margin, $margin, $margin);
$pdf->SetHeaderMargin($margin);
$pdf->SetFooterMargin($margin);
$pdf->SetAutoPageBreak(TRUE, $margin);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetFont('dejavusans', '', 9);

$html = '
	<br>
	<table width="100%">
		<tr>
			<td align="left">Date Range:</td>
			<td align="right">'.date('F d, Y',strtotime($dateFrom)).' to '.date('F d, Y',strtotime($dateTo)).'</td>
		</tr>
	</table>			
	<br><br>';

$html .= '
	<br><br> 					  
	<table cellpadding="5" width="100%" style="border-spacing: 0;">
		<tr align="center" style="font-size:10px;background-color: #f2f2f2" color="#a9a9a9">
			<th width="40%" align="left" style="border: 1px solid #e2e2e2">&nbsp;&nbsp;<b>PRODUCT</b></th>
			<th width="15%" style="border: 1px solid #e2e2e2"><b>UOM</b></th>
			<th width="15%" style="border: 1px solid #e2e2e2"><b>LOT NO</b></th>
			<th width="15%" style="border: 1px solid #e2e2e2"><b>EXP DATE</b></th>
			<th width="15%" style="border: 1px solid #e2e2e2"><b>BALANCE</b></th>
		</tr>';
	
$products = $stockcard->getProduct_expiry($dateFrom,$dateTo);
foreach($products as $k=>$v) {
	$html .= '
		<tr align="center" style="font-size:10px">
			<td align="left" style="vertical-align: middle; border: 1px solid #e2e2e2">&nbsp;&nbsp;'.$v['product_code'] .'
			<br>&nbsp;&nbsp;'.$v['product_description'].'</td>
			<td style="vertical-align: middle; border: 1px solid #e2e2e2">'.$v['unit_name'].'</td>
			<td style="vertical-align: middle; border: 1px solid #e2e2e2">'.$v['stock_lotno'].'</td>
			<td style="vertical-align: middle; border: 1px solid #e2e2e2">'.$v['stock_expiration_date'].'</td>
			<td style="vertical-align: middle; border: 1px solid #e2e2e2">'.$v['stock_qty'].'</td>																												
		</tr>';																								
}			

 $html .= '</table>';






if($_GET['action'] == "qqq"){
	$pdf->AddPage('P');
	
	$pdf->Image('../../static/logo.png', 15, 15, '', 12, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
	$meta = '
		<table align="right" width="100%" style="border-spacing: 0 5px">
			<tr>
				<td style="width:100%"><h4><b>Expiring Products</b></h4></td>
			</tr>
			<tr>
				<td>'.date('F d, Y ').'</td>
			</tr>
		</table>';

	$pdf->writeHTML($meta, true, false, true, false, '');

	$pdf->SetXY(15,34);
	
	$pdf->writeHTML($html, true, false, true, false, '');
	ob_end_clean();
	$pdf->Output('expired.pdf', 'I');
}

if($_GET['action'] == "export"){

        $backup_name = "expired.xls";
        header('Content-Type: application/octet-stream');   
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
        echo $html;
        exit;

}


?>