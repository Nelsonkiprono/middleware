<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Models\ClientsDetail;
use App\Models\Client;
use App\Models\LoanTransaction;
use App\Models\Journalentry;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leads=ClientsDetail::all();
        if($leads){
            return $leads;
        }
        else{
            return 'there are no leads';
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        $data = $request->all();
       
        $validator =  Validator::make($request->all(),[
            'branch' => 'required',
            'sub_branch' => 'required',
            'firstname' => 'required',
            'middlename' => 'required',
            'lastname' => 'required',
            'date_of_birth' => 'required',
            'id_number' => 'required|unique:m_leads',
            'phone' => 'required',
            'alternative_phone' => 'required',
            'Business_type' => 'required',
            'aproximate_stock_value' => 'required',
            'lattitude' => 'required',
            'longitude' => 'required',
            'monthly_business_rent' => 'required',
            'operation_duration' => 'required',
            'number_of_employees' => 'required',
            'best_daily_sales' => 'required',
            'average_daily_sales' => 'required',
            'worst_daily_sales' => 'required',
           
            'added_by' => 'required',
            'approval_status'=>'required'
        ]);
      
        if ($validator->fails()) {
            $err = $validator->errors();
            $err_meassage = array();
            foreach ($err->all() as $message) {
                array_push($err_meassage, $message);
            }
            return response()->json(['error' => $err_meassage], 400);
        }
// return response()->json([
//     "success"=>true,
//     "data"=>$data
// ]);
        $clientdetail = new ClientsDetail(); 
        if($data['approval_status']=="aproved"||"pending"||"Rejected"){
            $clientdetail->approval_status = $data['approval_status'];

        }
        $clientdetail->branch = $data['branch'];  
        $clientdetail->sub_branch = $data['sub_branch'];  
        $clientdetail->firstname = $data['firstname'];  
        $clientdetail->middlename = $data['middlename'];
        $clientdetail->lastname = $data['lastname'];
        $clientdetail->date_of_birth = $data['date_of_birth'];
        $clientdetail->id_number = $data['id_number'];
        $clientdetail->phone = $data['phone'];
        $clientdetail->alternative_phone = $data['alternative_phone'];
        $clientdetail->Business_type = $data['Business_type'];
        $clientdetail->aproximate_stock_value = $data['aproximate_stock_value'];
        $clientdetail->lattitude = $data['lattitude'];
        $clientdetail->longitude = $data['longitude'];
        $clientdetail->monthly_business_rent = $data['monthly_business_rent'];
        $clientdetail->operation_duration = $data['operation_duration'];
        $clientdetail->number_of_employees = $data['number_of_employees'];
        $clientdetail->best_daily_sales = $data['best_daily_sales'];
        $clientdetail->worst_daily_sales = $data['worst_daily_sales'];
        $clientdetail->average_daily_sales = $data['average_daily_sales'];
       
        $clientdetail->added_by =$data['added_by'];

        if($clientdetail->save()){
            
            return response(["msg" => "Data added Successfully", "data" => $clientdetail,"statusCode" => http_response_code()]);
        }else{
            return response(["msg" => "Data could not be added successfully","statusCode" => http_response_code()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $leads=ClientsDetail::select(
            'id','branch','sub_branch','firstname','middlename','lastname','date_of_birth','id_number','lattitude','longitude','monthly_residential_rent','phone',
            'Business_type','aproximate_stock_value','monthly_business_rent','operation_duration','number_of_employees','best_daily_sales','worst_daily_sales',
            'average_daily_sales','alternative_phone','approval_status','added_by','Rstatus',
            'Rlattitude','Rlongitude','marital_status','spause_name','spause_Id_no','spause_phone','no_of_dependants','referee1_name','referee1_id_no',
            'referee1_phone_no','referee1_relationship','referee2_name','referee2_id_no','referee2_phone_no','referee2_residence','referee2_relationship','created_at','updated_at'
        )->find($id);
        if($leads){
            return $leads;
        }
        else{
            return response(['message'=>'there are no lead']);
        }
    }



    public function searchSubmitted($id_number)
    {
        //
        $leads=ClientsDetail::select(
            'id','branch','sub_branch','firstname','middlename','lastname','date_of_birth','id_number','lattitude','longitude','monthly_residential_rent','phone',
            'Business_type','aproximate_stock_value','monthly_business_rent','operation_duration','number_of_employees','best_daily_sales','worst_daily_sales',
            'average_daily_sales','alternative_phone','approval_status','added_by','Rstatus',
            'Rlattitude','Rlongitude','marital_status','spause_name','spause_Id_no','spause_phone','no_of_dependants','referee1_name','referee1_id_no',
            'referee1_phone_no','referee1_relationship','referee2_name','referee2_id_no','referee2_phone_no','referee2_residence','referee2_relationship','created_at','updated_at'
        )->where('id_number',$id_number)->where('approval_status','submitted')->get()->first();
        if($leads){
            return $leads;
        }
        else{
            return response(['message'=>'there are no lead']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = $request->all();
        $validator =  Validator::make($request->all(),[
            'client_image' => 'required',
            'client_id_front_img' => 'required',
            'client_id_back' => 'required',
            'client_business_img' => 'required',
            'client_qn_form' => 'required',
            'Rstatus' => 'required',
            'Rlattitude' => 'required',
            'Rlongitude' => 'required',
            'monthly_residential_rent' => 'required',
            'marital_status' => 'required',
            'spause_name' => 'required',
            'spause_Id_no' => 'required',
            'spause_phone' => 'required',
            'no_of_dependants' => 'required',
            'referee1_name' => 'required',
            'referee1_id_no' => 'required',
            'referee1_phone_no' => 'required',
            'referee1_residence' => 'required',
            'referee1_residence' => 'required',
            'referee1_relationship' => 'required',
            'referee2_name' => 'required',
            'referee2_id_no' => 'required',
            'referee2_phone_no' => 'required',
            'referee2_residence' => 'required',
            'referee2_relationship' => 'required',
            'client_image' => 'required',
            'client_id_front_img' => 'required',
            'client_id_back' => 'required',
            'client_business_img' => 'required',
            'client_qn_form' => '',
        ]);
        if ($validator->fails()) {
            $err = $validator->errors();
            $err_meassage = array();
            foreach ($err->all() as $message) {
                array_push($err_meassage, $message);
            }
            return response()->json(['error' => $err_meassage], 400);
        }
    
        $dbclientdetail = ClientsDetail::where('id',$id)->find($id);

        if($request->hasFile('client_image')
         && $request->hasFile('client_id_front_img')
        && $request->hasFile('client_id_back')
        && $request->hasFile('client_business_img')
        && $request->hasFile('client_qn_form')
        ){

        $dbclientdetail->client_image = $request->file('client_image')->store('client_image', 'public');
        $dbclientdetail->client_id_front_img = $request->file('client_id_front_img')->store('client_id_front_img', 'public');
        $dbclientdetail->client_id_back = $request->file('client_id_back')->store('client_id_back', 'public');
        $dbclientdetail->client_business_img =$request->file('client_business_img')->store('client_business_img', 'public');
        $dbclientdetail->client_qn_form = $request->file('client_qn_form')->store('client_qn_form', 'public');
        }

       
        if(!empty($dbclientdetail)) {

        $dbclientdetail->Rstatus = $data['Rstatus'];
        $dbclientdetail->Rlattitude = $data['Rlattitude'];
        $dbclientdetail->Rlongitude = $data['Rlongitude'];
        $dbclientdetail->monthly_residential_rent = $data['monthly_residential_rent'];
        $dbclientdetail->marital_status =$data['marital_status'];
        $dbclientdetail->spause_name =$data['spause_name'];
        $dbclientdetail->spause_Id_no =$data['spause_Id_no'];
        $dbclientdetail->spause_phone =$data['spause_phone'];
        $dbclientdetail->no_of_dependants =$data['no_of_dependants'];

        $dbclientdetail->referee1_name =$data['referee1_name'];
        $dbclientdetail->referee1_id_no =$data['referee1_id_no'];
        $dbclientdetail->referee1_phone_no =$data['referee1_phone_no'];
        $dbclientdetail->referee1_residence =$data['referee1_residence'];
        $dbclientdetail->referee1_relationship =$data['referee1_relationship'];

        $dbclientdetail->referee2_name =$data['referee2_name'];
        $dbclientdetail->referee2_id_no =$data['referee2_id_no'];
        $dbclientdetail->referee2_phone_no =$data['referee2_phone_no'];
        $dbclientdetail->referee2_residence =$data['referee2_residence'];
        $dbclientdetail->referee2_relationship =$data['referee2_relationship'];

       
        $dbclientdetail->client_image = $data['client_image'];
        $dbclientdetail->client_id_front_img = $data['client_id_front_img'];
        $dbclientdetail->client_id_back = $data['client_id_back'];
        $dbclientdetail->client_business_img = $data['client_business_img'];
        $dbclientdetail->client_qn_form = $data['client_qn_form'];
        $dbclientdetail->approval_status = 'completed';
        $dbclientdetail->update();
        return response(["msg" => "Data added Successfully", "data" => $dbclientdetail,"statusCode" => http_response_code()]);
       
        }
    else{
        return response(["msg" => "Data could not be added successfully","statusCode" => http_response_code()]);
    }
        
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    // update aproved

   

    public function aprove($id)
    {
        $lead=ClientsDetail::findOrFail($id);
        if($lead->approval_status=='pending'){
            $lead->approval_status='aproved';
             $lead->update();
             return response(['res'=>'successfully updated']);

        }
        else{
            return response(['res'=>'there are no leads with the given id']);
        }
        
    }
    public function fsubmit($id)
    {
        $lead=ClientsDetail::findOrFail($id);
        if($lead->approval_status=='completed'){
            $lead->approval_status='submitted';
             $lead->update();
             return response(['res'=>'successfully updated']);

        }
        else{
            return response(['res'=>'there are no leads with the given id']);
        }
        
    }

    // public function submitted($id)
    // {
    //     $leadc=ClientsDetail::where('id', '=', $id)->first();
    //     if($leadc){
    //         $leadc->submitted=1;
    //          $leadc->update();
    //          return response(['res'=>'status successfully updated']);

    //     }
    //     else{
    //         return response(['res'=>'there are no leads with the given id']);
    //     }
        
    // }


    public function aproved()
    {
        $leads=ClientsDetail::where('approval_status','aproved')->get();
        if($leads){
            return $leads;
        }
        else{
            return response(['message'=>'there are no Aproved leads']);
        }
        
    }

    public function pending()
    {
        $leads=ClientsDetail::where('approval_status','pending')->get();
        if($leads){
            return $leads;
        }
        else{
            return response(['message'=>'there are no leads']) ;
        }
        
    }

    public function completed()
    {
        $leads=ClientsDetail::select(
            'id','branch','sub_branch','firstname','middlename','lastname','date_of_birth','id_number','lattitude','longitude','monthly_residential_rent','phone',
            'Business_type','aproximate_stock_value','monthly_business_rent','operation_duration','number_of_employees','best_daily_sales','worst_daily_sales',
            'average_daily_sales','alternative_phone','approval_status','added_by','Rstatus',
            'Rlattitude','Rlongitude','marital_status','spause_name','spause_Id_no','spause_phone','no_of_dependants','referee1_name','referee1_id_no',
            'referee1_phone_no','referee1_relationship','referee2_name','referee2_id_no','referee2_phone_no','referee2_residence','referee2_relationship','created_at','updated_at'
        )->where('approval_status','completed')->get();
        if($leads){
            return $leads;
        }
        else{
            return response(['message'=>'there are no leads']) ;
        }
        
    }


    public function reject($id)
    {
        $leads=ClientsDetail::findOrFail($id);
        if($leads){
        $leads->approval_status='rejected';
        $leads->update();
        return response(['res'=>'successfully updated']);}
        else{
            return response(['message'=>'there are no leads']) ;
        }
        
    }
    public function rejected()
    {
        $leads=ClientsDetail::where('approval_status','rejected')->get();
        if($leads){
            return $leads;
        }
        else{
            return response(['message'=>'there are no leads']) ;
        }
        
    }
     public function checkiftransidexists($code)
    {
        $item=Journalentry::where('transaction_id',$code)->count();
        if($item > 0){
            return response(['success'=>'1']) ;
        }
        else{
            return response(['success'=>'2']) ;
        }
        
    }
    public function loantransactions($id){
        if (isset($id)) {
            //
            $transactions = LoanTransaction::where('loan_id',$id)->where('interest_portion_derived','>',0)->where('principal_portion_derived','>',0)->where('is_reversed',0)->get();
             return $transactions ;
        }
        else{
            return response(['message'=>'check id']) ;
        }
      
      }
      public function defaulted($officeid){
        if (isset($officeid)) {
            $defaulted= DB::select(DB::raw(" SELECT c.id as clientid ,
             c.firstname ,
            c.lastname,
            c.mobile_no , 
            l.account_no AS loanAccountId,
            l.principal_amount AS loanamount ,
            o.id ,
            (IFNULL(l.principal_outstanding_derived, 0) + IFNULL(l.interest_outstanding_derived, 0) + IFNULL(l.fee_charges_outstanding_derived, 0) + IFNULL(l.penalty_charges_outstanding_derived, 0)) AS loanOutstanding,
            l.principal_disbursed_derived AS loanDisbursed,
            ls.duedate AS paymentDueDate,
            (IFNULL(SUM(ls.principal_amount),0) - IFNULL(SUM(ls.principal_writtenoff_derived),0)
             + IFNULL(SUM(ls.interest_amount),0) - IFNULL(SUM(ls.interest_writtenoff_derived),0) 
             - IFNULL(SUM(ls.interest_waived_derived),0)
             + IFNULL(SUM(ls.fee_charges_amount),0) - IFNULL(SUM(ls.fee_charges_writtenoff_derived),0) 
             - IFNULL(SUM(ls.fee_charges_waived_derived),0)
             + IFNULL(SUM(ls.penalty_charges_amount),0) - IFNULL(SUM(ls.penalty_charges_writtenoff_derived),0) 
             - IFNULL(SUM(ls.penalty_charges_waived_derived),0)
            ) AS totalDue,
            laa.total_overdue_derived AS totalOverdue
            FROM m_office o
            JOIN m_office ounder ON ounder.hierarchy LIKE CONCAT(o.hierarchy, '%')
            JOIN m_client c ON c.office_id = ounder.id
            JOIN m_loan l ON l.client_id = c.id
            LEFT JOIN m_staff lo ON lo.id = l.loan_officer_id
            LEFT JOIN m_currency cur ON cur.code = l.currency_code
            LEFT JOIN m_loan_arrears_aging laa ON laa.loan_id = l.id
            LEFT JOIN m_group_client gc ON gc.client_id = c.id
            LEFT JOIN m_group gp ON gp.id = l.group_id
            LEFT JOIN m_loan_repayment_schedule ls ON l.id = ls.loan_id
            WHERE o.id = :officeid AND l.loan_status_id = 300 AND l.expected_maturedon_date < CURDATE() 
            GROUP BY l.id
            ORDER BY l.account_no"), ['officeid' => $officeid]
);
             return $defaulted ;
        }
        else{
            return response(['message'=>'check id']) ;
        }
      
      }

      public function addclientstoerp(){
		
        //ini_set('max_execution_time', 216000); //3 minutes
        $clients = Client::all();
        //return json_decode($clients);
        foreach($clients as $client){
        
            $json = array();
    			
            $data = array('CustName' => $client->display_name,
                        'CustId' => $client->id,
                        'Address' => 'LIXNET',
                        'TaxId' => '',
                        'CurrencyCode' => 'KS',
                        'SalesType' => '1',
                        'CreditStatus' => '0',
                        'PaymentTerms' => '7',
                        'Discount' => '0',
                        'paymentDiscount' => '0',
                        'CreditLimit' => '0',
                        'Notes' => '');
            $endpoint='';
            $json[] = $data;
            $json_data = json_encode($json);
            $username = "api-user";
            $password = "admin";
            $headers = array(
                'Authorization: Basic '. base64_encode($username.':'.$password),
            );

            //Perform curl post request to add item to the accounts erp
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "$endpoint",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => $headers,
            ));

            $response = curl_exec($curl);
    
            curl_close($curl);
            
            $response_data = json_decode($response);

         
        } 
        return $response_data;
     }
    
}
