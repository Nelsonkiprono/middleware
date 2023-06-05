<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class OverpaymentController extends Controller
{
    /* 
        {\n	\"CustId\": \""+ loan.getClientId()+"\",\n	\"loanid\": \""+loan.getId()+"\",\n	\"TransDate\": \""+transactionDate+"\",\n	\"Amount\": \""+overpaymentt+"\"\n}
    
    */

    public $base_url = 'https://new-cbs.lixnet.net:8000/fineract-provider/api/v1/';
    public $username = 'admin';
  
    public $password = 'password';
    public $tenant = 'default';
  
    public $localkey = "YWRtaW46cGFzc3dvcmQ=";
    public $productionkey = "YWRtaW46cGFzc3dvcmQ=";
  
    public $localproductid = "1";
    public $productionproductid = "5";
  
    public $payed = 0;
    public $totalloan = 0;
    public $loancounter = 0;

    public function inittransferfromsavings(Request $request){
        $this->checksavingsAcForRepayment($request);

        // $myfile = fopen("loandisbursalrequest.json", "w") or die("Unable to open file!");
        // $txt = $request;
        // fwrite($myfile, $txt);
        
         
    }

    public function initsendtosavings(Request $request){ 
        $this->checksavingsAcForSaving($request);

        // $myfile = fopen("testapi.json", "w") or die("Unable to open file!");
        // $txt = $request;
        // fwrite($myfile, $txt);
    }
    
    //  Repay 
    public function checksavingsAcForSaving($request){
        $client_id = $request->CustId;
        $url = $this->base_url."clients/".$client_id."/accounts?username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

       
        // echo $resp;
        // die();
        
    
        $resp = json_decode($resp);
        
        // echo json_encode($resp->savingsAccounts);
        // die();
       
         if(!isset($resp->savingsAccounts)){
            $this->applyacfordeposit($client_id,$request);
            exit;
         }
            foreach($resp->savingsAccounts as $savingaccount){
                if($savingaccount->productId==4){
                    // Check the loan status
                    $response = $this->checkloanstatus($request);
                    if(isset($response->status->value) && $response->status->value=="Overpaid"){

                        //  Transfer from loan to savings
                      $this->fromLoantoSavingstransfer($savingaccount->id,$request);
                    }else{
                         $response = array("success"=>"false","message"=>"Loan is not overpaid!");
                         echo json_encode($response);
                    }

                    break;
                }else{
                    $response = array("success"=>"false","message"=>"Client does not have account");
                    echo json_encode($response);
                    exit;
                }
            }
        
    }

    public function checkloanstatus($request){
      
        $url = $this->base_url."loans/".$request->loanid."?username=admin&password=password&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
      
        $resp = json_decode($resp);
        
        return $resp;
    }

    // checksavings account

    public function checksavingsAcForRepayment($request){
        
        $url = $this->base_url."clients/".$request->CustId."/accounts?username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

         $resp = json_decode($resp);
      
        if(!isset($resp->savingsAccounts)){
            $response = array("success"=>"false","message"=>"Client does not have account");
            echo json_encode($response);
            exit;
        }

        foreach($resp->savingsAccounts as $savingaccount){
            
            if($savingaccount->productId==4){
                $balance=$this->checkSavingsAccountBalance($savingaccount->id);
                if($balance==0){
                    $response = array("success"=>"failed","message"=>"No balance");
                    echo json_encode($response);
                    exit;
                }else{
                  
                    $this->repayLoanFromSavings($savingaccount->id,$request->Loanid,$balance,$request->CustId,$request->TransDate);
                }
                break;
            }
            else{
                $response = array("success"=>"false","message"=>"Client does not have account");
                echo json_encode($response);
                exit;
            }
        }
        

    }


    public function checkSavingsAccountBalance($id){
        $url = $this->base_url."savingsaccounts/".$id."?username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        //  var_dump($resp); 
        $resp = json_decode($resp);
      
        return $resp->summary->accountBalance;

    }


    public function formatedate($date = ''){
    
        return date('j F Y',strtotime($date));
    }

  

    public function applyacfordeposit($client_id,$request){


        $url = $this->base_url."savingsaccounts?username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $TransDate = $this->formatedate($request->TransDate);
        $data = <<<DATA
        {
          "clientId": $client_id,
          "productId": 4,
          "locale": "en",
          "dateFormat": "dd MMMM yyyy",
          "submittedOnDate": "$TransDate"
        }
        DATA;
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);

     
        // echo $resp;
        // die();
        $resp = json_decode($resp);

        /*
         {"officeId":107,"clientId":8,"savingsId":692,"resourceId":692,"gsimId":0}
        */
        if(isset($resp->savingsId)){
            $this->approveacfordeposit($resp->savingsId,$request);
        }

    }

    // Approve savings account
    public function approveacfordeposit($account_id,$request){

        $url = $this->base_url."savingsaccounts/".$account_id."?command=approve&username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
        "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $TransDate = $this->formatedate($request->TransDate);

        $data = <<<DATA
        {
        "locale": "en",
        "dateFormat": "dd MMMM yyyy",
        "approvedOnDate": "$TransDate"
        }
        DATA;

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
      
        $resp = json_decode($resp);
        /*
        {"officeId":107,"clientId":8,"savingsId":692,"resourceId":692,"changes":{"status":{"id":200,"code":"savingsAccountStatusType.approved","value":"Approved","submittedAndPendingApproval":false,"approved":true,"rejected":false,"withdrawnByApplicant":false,"active":false,"closed":false,"prematureClosed":false,"transferInProgress":false,"transferOnHold":false,"matured":false},"locale":"en","dateFormat":"dd MMMM yyyy","approvedOnDate":"09 November 2022"}}
        */
        if(isset($resp->savingsId)){
            $this->activateacfordeposit($resp->savingsId,$request);
        }

    }

    // Activate savings account
    public function activateacfordeposit($account_id,$request){

        $url = $this->base_url."savingsaccounts/".$account_id."?command=activate&username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
        "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $TransDate = $this->formatedate($request->TransDate);
        $data = <<<DATA
        {
        "locale": "en",
        "dateFormat": "dd MMMM yyyy",
        "activatedOnDate": "$TransDate"
        }
        DATA;

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
    
        $resp = curl_exec($curl);
   
        // echo $request;
        // die();
        
        curl_close($curl);
        $resp = json_decode($resp);
        /*
        {"officeId":107,"clientId":8,"savingsId":692,"resourceId":692,"changes":{"status":{"id":300,"code":"savingsAccountStatusType.active","value":"Active","submittedAndPendingApproval":false,"approved":false,"rejected":false,"withdrawnByApplicant":false,"active":true,"closed":false,"prematureClosed":false,"transferInProgress":false,"transferOnHold":false,"matured":false},"locale":"en","dateFormat":"dd MMMM yyyy","activatedOnDate":"09 November 2022"}}
        */     
        if(isset($resp->officeId)){
            $this->fromLoantoSavingstransfer($account_id,$request);
        }
      
    }

    //  Transfer amount from savings account to loan account
    public function fromLoantoSavingstransfer($savingsid,$request){

     
        $url = $this->base_url."accounttransfers?username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $clientdetails = Client::where('id',$request->CustId)->first();
       
        $amount = preg_replace("/[^0-9.]/", "", $request->Amount);

        $office_id = $clientdetails->office_id;
        $transaction_date = $this->formatedate($request->TransDate);
        $data = <<<DATA
        {
            "dateFormat":"dd MMMM yyyy",
            "fromAccountId": "$request->loanid",
            "fromAccountType": 1,
            "fromClientId": $request->CustId,
            "fromOfficeId": $office_id,
            "locale": "en",
            "toAccountId": $savingsid, 
            "toAccountType": 2,
            "toClientId": $request->CustId,
            "toOfficeId": $office_id,
            "transferAmount": "$amount",
            "transferDate": "$transaction_date",
            "transferDescription": "transfer money from loan to savings"
        }
        DATA;
        
       
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        
        // $myfile = fopen("overpaymentresponse.json", "w") or die("Unable to open file!");
        // $txt = $resp;
        // fwrite($myfile, $txt);

        // exit;
        curl_close($curl);
        $resp = json_decode($resp);
        
        if(isset($resp->loanId)){
            $response = array("success"=>"true","message"=>"Amount has been transfered successfully!","Response"=>$resp);
            echo json_encode($response);
        }else{
            $response = array("success"=>"false","message"=>"Transefer failed","Response"=>$resp);
            echo json_encode($response);
        }
              
    }

    // Transfer amount from savings to a loan
    public function repayLoanFromSavings($savingsid,$loanid,$amount,$client_id,$trans_date){

        $url = $this->base_url."accounttransfers?username=".$this->username."&password=".$this->password."&tenantIdentifier=default";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Authorization: Basic YWRtaW46cGFzc3dvcmQ=",
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $clientdetails = Client::where('id',$client_id)->first();
      
        $office_id = $clientdetails->office_id;

        $data = <<<DATA
        {
            "dateFormat":"dd MMMM yyyy",
            "fromAccountId": "$savingsid",
            "fromAccountType": 2,
            "fromClientId": $client_id,
            "fromOfficeId": $office_id,
            "locale": "en",
            "toAccountId": $loanid, 
            "toAccountType": 1,
            "toClientId": $client_id,
            "toOfficeId": $office_id,
            "transferAmount": "$amount",
            "transferDate": "$trans_date",
            "transferDescription": "Repayment from the savings account to client account"
        }
        DATA;
        
      
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);


        curl_close($curl);
        $resp = json_decode($resp);

 
        
        if(isset($resp->savingsId)){
            $response = array("success"=>"true","message"=>"Amount has been transfered successfully!","Response"=>$resp);
            echo json_encode($response);
        }else{
            $response = array("success"=>"false","message"=>"An error occurred!","Response"=>$resp);
            echo json_encode($response);
        }
              
}


}
