<?php

$_GET['action'] = "export";
require_once('tcpdf_include.php');
require_once "../../controller/controller.db.php";
require_once "../../model/model.report.php";

$stockcard = new Stockcard();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Total Documents Reports');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

$pdf->SetHeaderData('', '', '', '');


$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}
// define ('PDF_PAGE_FORMAT', 'LETTER');
$pdf->SetFont('dejavusans', '', 9);

	$html = '<table align="center" width="100%">
			
			<tr>
				<td style="width:100%"><h4><b>SUMMARY REPORTS</h4></b></td>
			</tr>
		</table>			
		
			<br><br><br>';

		$html .= '<table cellpadding="3" width="100%">
					<tr>
						<td><p><b>Date:</b> <span>'.date('F d, Y').'</span> </p></td>
						
					</tr>
				  </table>
		

				  
		 <table cellpadding="1" width="100%">

		 	<br>
		 	<br>
		 	
			<tr align="center" style="font-size:10px">
				<th style="border-left-width:0px;border-right-width:1px;border-bottom-width:0px;border-top-width:0px;"><b>PRODUCT CODE</b></th>
				<th style="border-right-width:1px;border-bottom-width:0px;border-top-width:0px;" ><b>DESCRIPTION</b></th>
				<th style="border-right-width:1px;border-bottom-width:0px;border-top-width:0px;" ><b>UNIT</b></th>
				<th style="border-right-width:1px;border-bottom-width:0px;border-top-width:0px;" ><b>CATEGORY</b></th>
				<th style="border-right-width:1px;border-bottom-width:0px;border-top-width:0px;" ><b>REMAINING BALANCE</b></th>
				
			
			</tr>';
	
	
$products = $stockcard->getSummary();

foreach($products as $k=>$v) {

	$quantity = $v['quantity'];

	if($quantity=="" || $quantity==null){
		$quantity = 0;
	}

	$html .= '<tr align="center" style="font-size:10px">

							<td style="border-left-width:0px;border-right-width:1px;border-bottom-width:0px;">'.$v['product_code'].'</td>
							<td style="border-right-width:1px;border-bottom-width:0px;">'.$v['product_description'].'</td>
							<td style="border-right-width:1px;border-bottom-width:0px;">'.$v['unit_name'].'</td>
							<td style="border-right-width:1px;border-bottom-width:0px;">'.$v['category_name'].'</td>
							<td style="border-right-width:1px;border-bottom-width:0px;">'.$quantity.'</td>
							
																																			
						  </tr>';	

}
						  					  					  					  					  					  
							  
 $html .= '</table>';






if($_GET['action'] == "qqq"){
$pdf->AddPage('P');
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output('summary.pdf', 'I');
}

if($_GET['action'] == "export"){

        $backup_name = "summary.xls";
        header('Content-Type: application/octet-stream');   
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
        echo $html;
        exit;

}


?>