<?php
include 'database.php';

class printcontroller {

	public function printledger($loanid){
		$mysqli = $this->mysqli();
		$loanid = 1;
		$sql = $mysqli->prepare("SELECT *FROM tbl_loandetails WHERE LoanId=:LoanId");
		$sql->bindParam(":LoanId",$loanid);
		$sql->execute();
		$result=$sql->fetchAll();
		$data = array();
		foreach($result  as $x){
			$amount = explode(',',$x['Amount']);
			$amount = $amount[0] + $amount[1];
			$array = array();
			$array['Date'] = $x['PaymentDate'];
			$array['Particular'] = $x['Particular'];
			$array['Amount'] = $amount;
			$data[] = $array;
		}
		return $data;
	}
}
?>