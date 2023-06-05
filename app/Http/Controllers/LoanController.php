<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\DummyLoan;
use App\Models\Loan;
use App\Models\Payment;

class LoanController extends Controller
{
    //  Loan Product ID

  public $base_url = 'https://new-cbs.lixnet.net:8000/fineract-provider/api/v1/';
  public $username = 'admin';

  public $password = 'password';
  public $tenant = 'default';

  public $localkey = "bWlmb3M6cGFzc3dvcmQ=";
  public $productionkey = "YWRtaW46cGFzc3dvcmQ=";

  public $localproductid = "1";
  public $productionproductid = "5";

  public $payed = 0;
	public $totalloan = 0;
	public $loancounter = 0;


 
	
	// Apply per loan criteria


	public function initializeperloan(){
		$i=1;
		for($i=1; $i < 31 ; $i++) {
	 	$totalamount = 0;
		$startdate="2022-10-01";
		$date=date('Y-m-d', strtotime($startdate. ' + $i days'));
		$loans = $this->getClientLoans($date);

		
		foreach($loans as $loan){
			//$phone2 = $this->formatnumber($loan->phone);
			$this->setCount();
			// dd($this->formatnumber($loan->phone));
			if (isset($loan->phone)) {
				$client = $this->getClient($loan->phone);
				// dd($client);
			}
			
			if (isset($client)) {
				$checkLoanExists = $this->checkLoanExists($client->id,$loan);
				if($checkLoanExists == 0){	
					$this->applyLoan($client->id,$loan);
			    }
			    else{
			    	$this->updateAppliedLoan($loan->phone);
			    	echo "Loan exists:".$this->getCount()."</br>";
			    }
			} else{

				echo "Client does not exist:".$this->getCount()."Name = ".$loan->client_name."|||phone = ".$loan->phone."</br>";
				var_dump($client);
			}
		}
	
		}
	}



	//Function to initialize the repayments per loan.

	//  Less than or equal to
	//  Check if payment has already been made.
	//  Use outstanding 
   	//  

   	// Function to check if the loan has partial / full payment. If outstandng < the total amount to be re
	public function initloanpayment(){
		$i=1;
		for($i=1; $i < 31 ; $i++) {
	 	$totalamount = 0;
		$startdate="2022-10-01";
		$date=date('Y-m-d', strtotime($startdate. ' + $i days'));
	 	$loans = $this->getApprovedLoans($date);
		//echo json_encode($loans);
   
	 	foreach($loans as $loan){
	 		if($this->checkOutstanding($loan->id)==1){
	 		$start_date = $this->formatedate4($loan->disbursedon_date);
	 		$end_date = $this->formatedate4($loan->expected_maturedon_date);

	 		$totaltoberepaid = $this->getLoanTotalAmount($loan->id);
			$startdate = $this->formatedate2($start_date);
			$enddate = $this->formatedate2($end_date);
	 		if (isset($loan->client->mobile_no)) {
	 			$repayments = $this->getRepayments($loan->client->mobile_no,$startdate,$enddate);
	 		}
			echo json_encode($repayments);
	 		
	 		if(isset($repayments)){
	 		
	 		foreach($repayments  as $repayment){
			echo json_encode($repayment);
			
	 			$repayment->date = $this->formatedate3($repayment->date);

	 			 
	 			 
	 			  $dateTimestamp1 = strtotime($end_date);
                   $dateTimestamp2 = strtotime($start_date);

	 			  $repdate = strtotime($repayment->date);
	 			if ($repdate > $dateTimestamp2 && $repdate < $dateTimestamp1 ) {
	 				if ($totalamount>=$totaltoberepaid) {
	 					$totalamount = 0;
	 					//echo "</br></br>";
	 					//echo "Loan already payed: Total to be repaid=".$totaltoberepaid."Total amount paid:".$totalamount;
	 				}else{
					
	 					$this->repay($loan->id,$repayment);
					  $totalamount+= (double)$repayment->amount;
	 						//echo json_encode($repayment)."</br></br></br></br>";
	 					
	 				}
	 				
	 			}
	 			}
	 		}
	 	}
	 		
	 	}
	}
	}


   public function initpartialpayment(){
	 	$totalamount = 0;
		$repaidamount=0;
		$i=1;
		for($i=1; $i < 31 ; $i++) {
	 	$totalamount = 0;
		$startdate="2022-10-01";
		$date=date('Y-m-d', strtotime($startdate. ' + $i days'));
	 	$loans = $this->getApprovedLoans($date);
		// echo json_encode($loans);
   
	 	foreach($loans as $loan){
	 		if($this->checkOutstanding($loan->id)==2){
			$repaidamount=$this->getrepaid($loan->id);
	 		$start_date = $this->formatedate4($loan->disbursedon_date);
	 		$end_date = $this->formatedate4($loan->expected_maturedon_date);

	 		$totaltoberepaid = $this->getLoanTotalAmount($loan->id);
			$startdate = $this->formatedate2($start_date);
			$enddate = $this->formatedate2($end_date);
	 		if (isset($loan->client->mobile_no)) {
	 			$repayments = $this->getRepayments($loan->client->mobile_no,$startdate,$enddate);
	 		}
			echo json_encode($repayments);
			echo json_encode($loan->client->mobile_no);
			if(!empty($repayments)){
	 		//echo $loan->client->mobile_no;
	 		
	 		 $dateTimestamp1 = strtotime($end_date);
              $dateTimestamp2 = strtotime($start_date);
			 // echo json_encode($end_date);
			//echo json_encode($start_date);
	 		foreach($repayments  as $repayment){
			//echo json_encode($repayment);
			
	 			 //$repaydate = $this->formatedate3($repayment->date);
				 //$repdate = strtotime($repaydate);
   //if ($repdate > $dateTimestamp2 && $repdate < $dateTimestamp1 ) {
	 			  			
	 				if ($repaidamount >= $totaltoberepaid) {
	 					echo "Loan already payed: Total to be repaid=".$totaltoberepaid."Total amount paid:".$repaidamount;
						$repaidamount = 0;
	 			}else{
	 					$lid=$this->repay($loan->id,$repayment);
						if($lid){
						$repaidamount += $repayment->amount; 
						 echo json_encode($repayment);
	 						} 
							//$this->repay($loan->id,$repayment);
	 				//}
	 				
	 			}
				}
				}else{
				echo "empty";
					
	 		}
	 	}
	 		
	 	}
	}
	}

	public function getLoanTotalAmount($loanId){
	    //set_time_limit(0);
		$url = $this->base_url."loans/".$loanId."?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant;

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
			   "Authorization: Basic ".$this->productionkey,
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 0);

			$resp = curl_exec($curl);
			curl_close($curl);
			// var_dump($resp);
			$data = json_decode($resp, true);
			if(isset($data['summary'])){
			 return $data['summary']['totalExpectedRepayment'];
			}
			else{
				echo "</br></br>";
				var_dump($resp);
			}
			
	}

	public function getrepaid($loanId){
		$url = $this->base_url."loans/".$loanId."?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant ;

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
			   "Authorization: Basic ".$this->productionkey,
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);

			$resp = curl_exec($curl);
			// dd($resp);
			curl_close($curl);
			// var_dump($resp);
			$data = json_decode($resp, true);
			if(isset($data['summary'])){
				 //$totalamount = $data['summary']['totalExpectedRepayment'];
				// $totaloutstanding = $data['summary']['totalOutstanding'];
				  $repaid = $data['summary']['totalRepayment'];
				//$repaid=$totalamount-$totaloutstanding;
				return $repaid;
				 
			}else{
				return 0;
			}	 
			
	}

	// Adding the counter
	public function setCount(){
		$this->loancounter+=1;
	}
	// Getting the counter
	public function getCount(){
		return $this->loancounter;
	}
	public function checkOutstanding($loanId){
		$url = $this->base_url."loans/".$loanId."?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant ;

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
			   "Authorization: Basic ".$this->productionkey,
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			//for debug only!
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);

			$resp = curl_exec($curl);
			// dd($resp);
			curl_close($curl);
			// var_dump($resp);
			$data = json_decode($resp, true);
			if(isset($data['summary'])){
				 $totalamount = $data['summary']['totalExpectedRepayment'];
				 $totaloutstanding = $data['summary']['totalOutstanding'];
				 if ($totaloutstanding == $totalamount) {
				 	return 1;
				 }
				 elseif ($totaloutstanding > 0 && $totaloutstanding <= $totalamount) {
				 	return 2;
				 }
				 else{
				 	return 0;
				 }
			}else{
				echo "</br></br>";
				var_dump($resp);
			}	 
			
	}

	// Get one client

	public function getClient($phone = ''){

		 $client = Client::where('mobile_no',$phone)->first();

		 return $client;
	}

   public function getTransactions($loanId = ''){

		$url =$this->base_url."loans/".$loanId."?associations=all&exclude=guarantors,futureSchedule&username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant;
		$trans_dates = [];
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Authorization: Basic ".$this->productionkey,
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 0);

		$resp = curl_exec($curl);
		$data = json_decode($resp, true);
		curl_close($curl);
		// dd($data['transactions']);
		
		foreach($data['transactions'] as $trans){
			// echo "</br></br></br>";
			$date= $trans['date'][0]."-".$trans['date'][1]."-".$trans['date'][2];
			$formatteddate = $this->formatedate2($date);
			array_push($trans_dates, $formatteddate);
		}

		
		/*
			Get all the transations
			Get all the dates / receipts of a transaction.

			Get the repayments of the loan where the date is not in the date fetched from transactions 

			Initialize the loan repayment

		*/
	
		return $trans_dates;
	}
	// Get the loans for a client
	public function getClientLoans($date){
			$loans = DummyLoan::where('applied','0')->whereDate('start_date',$date)->get();
			//$loans = DummyLoan::where('applied','0')->get();
			return $loans;
	}

	public function updateAppliedLoan($phone){
		  DummyLoan::where("phone", $phone)->update(["applied" => "1"]);
	}
	 // Funtion to get repayments of a loan

	 public function getRepayments($phone,$startdate,$enddate){
	 	// implode(', ', $mystaff)
	 	// ->whereNotIn('date',implode(', ', $mystaff));
	 	//$payments = Payment::where('used','0')->where('phone',$phone)->where('date','<=',$enddate)->where('date','>',$startdate)->get();
	 	$payments = Payment::where('used','0')->where('phone',$phone)->whereDate('date','>',$startdate)->whereDate('date','<=',$enddate)->get();
		return $payments;
	 }

	 // Function to get all the loans 
	 public function getApprovedLoans($date){

	 	//$loans = Loan::with('client')->where('loan_status_id',300)->whereDate('submittedon_date', '>','2022-04-01')->whereDate('submittedon_date', '<','2022-06-31')->get();
		$loans = Loan::with('client')->where('loan_status_id',300)->whereDate('activation_date','>',$date)->get();

	 	return $loans;

	 }

	// format date to i.e 15 August 2022
	public function formatedate($date = ''){
  
        return date('j F Y',strtotime($date));
    }

   // format date to i.e 2022-07-15
	public function formatedate2($date = ''){
  
        return date('Y-m-d',strtotime($date));
    }

   // format date to i.e 2022-07-15
	public function formatedate3($date = ''){
  		     
      //       $string = $date;
		// $str_arr = explode ("/", $string); 
		
		// $realdate = $str_arr[0]."-".$str_arr[1]."-20".$str_arr[2];
		
        return date('d-m-Y',strtotime($date));
    }

 // format date to i.e 2022-07-15
	public function formatedate4($date = ''){
  
        return date('d-m-Y',strtotime($date));
    }
    // remove decimals and comma from amount
   public function formatamount($var = ''){
		$var = intval(preg_replace('/[^\d.]/', '', $var));
		return $var;
    }

 // remove decimals and comma from amount
    public function formatnumber($var = ''){
		 preg_replace('/[^A-Za-z0-9\-]/', '', $var);
		 $res = str_replace( array( '\'', '"',
      ',' , '-', ' ', '>' ), ' ', $var);
		 $res1 = preg_replace('/[^A-Za-z0-9\-]/', '', $res);
		return $res1;
    }

	// Add the total amount payed for current loan
    public function setPayed($amount){
    	$this->payed=$this->payed+$amount;
    }

    // Get the total amount payed for current loan
    public function getPayed(){
    	return $this->payed;
    }

    // Reset the payed amount
    public function resetPayed(){
    	$this->payed = 0;
    }
	// Check the number of loans that the client has
	public function getLoanNo($phone = '')
	{
		$number = DummyLoan::where('phone',$phone)->count();
		return $number;
	}

	// Get the date for start of payment 
	// public function startofpaymentdate($date = '')
	// {
	// 	 $date = date_create("10-Aug-22");
	//      date_add($date, date_interval_create_from_date_string("1 days"));
	 
	//      return date_format($date, "Y-m-d");
	// }

	// Getting the client status
	public function clientstatus($id)
	{
         $client = Client::where('id',$id)->first();

         return $client->payed_status;
    }

    // Update to 1 if a particular loan has been used.
    public function updateUsed($receipt_no,$resp)
    {
    	$affectedRows = Payment::where("receipt_no", $receipt_no)->update(["used" => "1"]);
    	if ($affectedRows) {
    		var_dump(json_encode($resp));
    	}
    }

    // Check if loan exists in the databases before application
    public function checkLoanExists($id,$loan){
    	if (isset($loan->start_date)) {
    		$presentloan = Loan::where('client_id',$id)->where('submittedon_date',$this->formatedate2($loan->start_date))->count();

    	     return $presentloan;
    	}
    
    }

    // Check if loan exists from the api

    public function loanExists($id,$loan)
    {
	 	$url = "https://new-cbs.lixnet.net:8000/fineract-provider/api/v1/loans?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant;
 
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Authorization: Basic ".$this->productionkey,
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 0);

		$resp = curl_exec($curl);
		curl_close($curl);
		//var_dump($resp);
    }


	// Apply loan for a client
	public function applyLoan($id,$loan){
		
		ini_set('max_execution_time', 0); //3 minutes
		$url = $this->base_url."loans?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant;

		$date = $this->formatedate($loan->start_date);


		$amount = $this->formatamount($loan->amount);

		

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 0);

		$headers = array(
		   "Authorization: Basic ".$this->productionkey,
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = <<<DATA
		{
		  "clientId": $id,
		  "productId": 5,
		  "disbursementData": [],
		  "principal": $amount,
		  "loanTermFrequency": 30,
		  "loanTermFrequencyType": 0,
		  "numberOfRepayments": 30,
		  "repaymentEvery": 1,
		  "repaymentFrequencyType": 0,
		  "interestRatePerPeriod": 25,
		  "amortizationType": 1,
		  "isEqualAmortization": false,
		  "interestType": 1,
		  "interestCalculationPeriodType": 1,
		  "allowPartialPeriodInterestCalcualtion": false,
		  "transactionProcessingStrategyId": 1,
		  "rates": [],
		  "locale": "en",
		  "dateFormat": "dd MMMM yyyy",
		  "loanType": "individual",
		  "expectedDisbursementDate": "$date",
		  "submittedOnDate": "$date"
		}
		DATA;

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($resp);
	
		if(isset($resp->loanId)){
			$this->approve($resp->loanId,$loan);
		}
		
	}

	// Approve all loans 

	public function approve($loanId = '',$loan){


		$url = $this->base_url."loans/".$loanId."?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant."&command=approve";

		$date = $this->formatedate($loan->start_date);


		$amount = $this->formatamount($loan->amount);


		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Authorization: Basic ".$this->productionkey,
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = <<<DATA
		{
		  "approvedOnDate": "$date",
		  "approvedLoanAmount": $amount,
		  "note": "",
		  "expectedDisbursementDate": "$date",
		  "disbursementData": [],
		  "locale": "en",
		  "dateFormat": "dd MMMM yyyy"
		}
		DATA;

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 0);

		$resp = curl_exec($curl);
		curl_close($curl);

		$resp = json_decode($resp);

		if(isset($resp->loanId)){
			$this->dispurse($resp->loanId,$loan);
		}

	}
		// Dispurse all loans

	 public function dispurse($loanId = '',$loan){

	 	$url = $this->base_url."loans/".$loanId."?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant."&command=disburse";

	 	$date = $this->formatedate($loan->start_date);


		$amount = $this->formatamount($loan->amount);

		$receipt = $loan->trans_no;

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Authorization: Basic ".$this->productionkey,
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT, 0);

		$data = <<<DATA
		{
		  "paymentTypeId": 1,
		  "transactionAmount": $amount,
		  "actualDisbursementDate": "$date",
		  "receiptNumber": "$receipt",
		  "locale": "en",
		  "dateFormat": "dd MMMM yyyy"
		}
		DATA;

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		echo "</br></br>";
		var_dump($resp);

	 }

	 	 // Funtion to repay loan
	 

	 public function repay($loanId='',$repayment){
	 	$url = $this->base_url."loans/".$loanId."/transactions?username=".$this->username."&password=".$this->password."&tenantIdentifier=".$this->tenant."&command=repayment";
			  if(isset($repayment->amount)){
			 	$amount = $this->formatamount($repayment->amount);
			 } 
			 //$amount = $repayment->amount;
			 
			 
			 
			 
			 if(isset($repayment->date)){
			 	$date = $this->formatedate($repayment->date);
			 }
			 if(isset($repayment->receipt_no)){
			 	$receipt =$repayment->receipt_no;
			 }
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		

		$headers = array(
		   "Authorization: Basic ".$this->productionkey,
		   "Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$data = <<<DATA
		{
		  "paymentTypeId": 1,
		  "transactionAmount": $amount,
		  "transactionDate": "$date",
		  "receiptNumber": "$receipt",
		  "locale": "en",
		  "dateFormat": "dd MMMM yyyy"
		}
		DATA;

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		$resp = curl_exec($curl);
		curl_close($curl);

		$resp = json_decode($resp);

		if(isset($resp->loanId)){

			 $this->updateUsed($repayment->receipt_no,$resp);
			 //echo "</br></br>";
			 //var_dump($resp);
			 return $resp->loanId;

		}else{
		return false;
		}

	 }
	 public function saveclients(){
        //$clients = DummyClient::where('subregion',404)->get();
		$clients = DummyClient::where('subregion',404)->get();
     
        // echo $clients;
        // die();
        foreach($clients as $client){

            $str_arr = explode (" ", $client->name); 
        
            $firstname = $str_arr[0];
            $middlename =$str_arr[1];
            $lastname = $str_arr[2];

            $client->firstname = $firstname;
            $client->middlename = $middlename;
            $client->lastname = $lastname;

            if ($lastname==null) {
                $client->lastname = $middlename;
            }
           
            if (is_numeric($client->subregion)) {
                $this->saveData($client);
            }
            
        }
    }

    public function saveData($client){
        
        $firstname  = preg_replace('/\s+/', '', $client->firstname);
        $lastname = preg_replace('/\s+/', '', $client->lastname);
        $middlename  = preg_replace('/\s+/', '', $client->middlename);
        $id_no  = preg_replace('/\s+/', '', $client->id_no);
        $mobile_no  = preg_replace('/\s+/', '', $client->mobile_no);
        $date  = trim($client->reg_date);
        $office_id  = preg_replace('/\s+/', '', $client->subregion);


  
        $url = "https://new-cbs.lixnet.net:8000/fineract-provider/api/v1/clients?username=admin&password=password&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
           "Content-Type: application/json",
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = <<<DATA
          {
            "officeId": $office_id,
            "firstname": "$firstname",
            "middlename": "$middlename",
            "legalFormId":1,
            "lastname": "$lastname",
            "externalId": "$id_no",
            "mobileNo":"$mobile_no",
            "dateFormat": "dd MMMM yyyy",
            "locale": "en",
            "active": true,
            "activationDate": "$date",
            "submittedOnDate":"$date"
            }
        DATA;
      


        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        var_dump($resp);
        echo "</br>";

    }


	}