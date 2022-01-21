<?php
$_GET['action'] = 'pdf';
$stock_id = $_GET['stock_id'];
$product_id = $_GET['product_id'];
require_once('tcpdf_include.php');
require_once "../../controller/controller.db.php";
require_once "../../model/model.report.php";

$stockcard = new Stockcard();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Progressive Medical Corporation');
$pdf->SetTitle('Stock Card Report');
$pdf->SetSubject('Stock card report');


$pdf->SetPrintFooter(true);
$pdf->SetPrintHeader(false);

$margin = 15;

$pdf->SetMargins($margin, $margin, $margin);
$pdf->SetHeaderMargin($margin);
$pdf->SetFooterMargin($margin);


$pdf->SetAutoPageBreak(TRUE, $margin);


$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('dejavusans', '', 9);


	$html = "";


	$product_header = $stockcard->getProductDetails($product_id);
	$lot_no = ($stockcard->getlotno($stock_id)) ?: "n/a";
	$stockcard = $stockcard->getStockcard($stock_id);

	/* stock card meta */
	$html .= '	
		<table style="border-spacing: 0 5px" width="100%">
			<tr>
				<td width="50%" align="left">'.$product_header['product_description'].'</td>
				<td width="50%" align="right"><span style="font-size: 10px;" color="#a9a9a9"><b>LN/SN</b></span>: '.$lot_no.'</td>
			</tr>
			<tr>
				<td width="50%" align="left">'.$product_header['product_code'].'</td>
				<td width="50%" align="right"><span style="font-size: 10px;" color="#a9a9a9"><b>UoM</b></span>: '.$product_header['unit'].'</td>
			</tr>
		</table>';
		
	/* Report table head  */
	$html .= '
		<br><br><br>
		<table cellpadding="5" width="100%" style="border-spacing: 0;">
			<tr align="center" style="font-size:10px;background-color: #f2f2f2" color="#a9a9a9">
				<th style="border: 1px solid #e2e2e2">DATE</th>
				<th style="border: 1px solid #e2e2e2">TIME</th>
				<th style="border: 1px solid #e2e2e2">LN/PN</th>
				<th style="border: 1px solid #e2e2e2">SN</th>
				<th style="border: 1px solid #e2e2e2">EXP DATE</th>
				<th style="border: 1px solid #e2e2e2">IN</th>
				<th style="border: 1px solid #e2e2e2">OUT</th>
				<th style="border: 1px solid #e2e2e2">BALANCE</th>
				<th style="border: 1px solid #e2e2e2">REF-IN</th>
				<th style="border: 1px solid #e2e2e2">REF-OUT</th>
				<th style="border: 1px solid #e2e2e2">REMARKS</th>
				<th style="border: 1px solid #e2e2e2">APPROVER</th>
				<th style="border: 1px solid #e2e2e2">END USER</th>
			</tr>';

	/* Populate Report */
	if(empty($stockcard)) {
		
		$html .= '
		<tr align="center" style="font-size:10px;">
			<td colspan="13" style="border: 1px solid #e2e2e2" color="#a9a9a9">No data available</td>																														
		</tr>';

	} else {
		$stock_balance = 0;
		foreach($stockcard as $k=>$v) {
			$log_type = $v['log_type'];
			
			$stock_in = $stock_out = $reference_in = $reference_out = "";
			if($log_type=="in"){
				$stock_in = $v['log_qty'];
				$reference_in = $v['log_reference'];
				$stock_balance = $stock_in + $stock_balance; 
			} else {
				$stock_out = $v['log_qty'];
				$reference_out = $v['log_reference'];
				$stock_balance = $stock_balance - $stock_out;
			}

			$even = ($k % 2 == 0) ? '' : 'background-color: #f2f2f2';

			$html .= '
				<tr align="center" style="font-size:10px; '.$even.'">
					<td style="border: 1px solid #e2e2e2">'.$v['log_transaction_date'].'</td> 
					<td style="border: 1px solid #e2e2e2">'.$v['log_transaction_time'].'</td>
					<td style="border: 1px solid #e2e2e2">'.$v['stock_lotno'].'</td>
					<td style="border: 1px solid #e2e2e2">'.$v['stock_serialno'].'</td>
					<td style="border: 1px solid #e2e2e2">'.$v['stock_expiration_date'].'</td>
					<td style="border: 1px solid #e2e2e2">'.$stock_in.'</td>
					<td style="border: 1px solid #e2e2e2">'.$stock_out.'</td>
					<td style="border: 1px solid #e2e2e2">'.$stock_balance.'</td>
					<td style="border: 1px solid #e2e2e2">'.$reference_in.'</td>
					<td style="border: 1px solid #e2e2e2">'.$reference_out.'</td>
					<td style="border: 1px solid #e2e2e2">'.$v['log_notes'].'</td>
					<td style="border: 1px solid #e2e2e2">'.$v['approver'].'</td>
					<td style="border: 1px solid #e2e2e2">'.$v['end_user'].'</td>																															
				</tr>';					  	
		}				  					  					  					  					  					  
	}				  

 	$html .= '</table>';

	if($_GET['action'] == "pdf"){

		/* display logo */
		$pdf->AddPage('L', 'A4');
		$pdf->Image('../../static/logo.png', 15, 15, '', 12, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
		
		$meta = '
			<table align="right" width="100%" style="border-spacing: 0 5px">
				<tr>
					<td style="width:100%"><h4><b>Stock Card</b></h4></td>
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


?>