<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Report;
use App\Models\Client;

class IntegrationController extends Controller
{
   
  public function mark_fav($rep_id = ''){
    	$affectedRows = Report::where("id", $rep_id)->update(["report_subtype" => "1"]);
         	if ($affectedRows) {
         		
         	}
  }
	
  public function unmark_fav($rep_id = ''){
    	$affectedRows = Report::where("id", $rep_id)->update(["report_subtype" => "0"]);
       	if ($affectedRows) {
       		
       	}
  }

  public function recover(Request $request){
      $affectedRows = Client::where("id", $request->client_id)->update(["recovered" => "200",
        "recovered_date" => $request->recovered_date,
        "recovery_note" => $request->note]);
        if ($affectedRows) {
          $response = array("status" => "ok", "message" => "Client Details updated successfully");
          echo json_encode($response);
        }
  }

  public function exitclient(Request $request){
      $affectedRows = Client::where("id", $request->client_id)->update(["exited" => "200",
        "exit_date" => $request->exit_date,
        "exit_note" => $request->note]);
        if ($affectedRows) {
          $response = array("status" => "ok", "message" => "Client Details updated successfully");
          echo json_encode($response);
        }
  }

  public function testhook(Request $request){
      $json = file_get_contents('php://input');

      $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
      $txt = "John Doe\n";
      fwrite($myfile, $json);
      fclose($myfile);

      // $affectedRows = Report::where("id", '1')->update(["report_subtype" => "414341"]);
  }

  public function getDup(){
      $limo = Loan::all();

      echo $limo;
  }


}
