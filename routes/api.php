<?php

use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/mark_fav/{id}', [App\Http\Controllers\IntegrationController::class, 'mark_fav'])->name('mark_fav'); Route::post('/unmark_fav/{id}', [App\Http\Controllers\IntegrationController::class, 'unmark_fav'])->name('unmark_fav');
Route::resource('clientdetail', App\Http\Controllers\ClientController::class);
Route::get('pending', 'App\Http\Controllers\ClientController@pending');
Route::get('aproved', 'App\Http\Controllers\ClientController@aproved');
Route::get('completed', 'App\Http\Controllers\ClientController@completed');
Route::put('submit/{id}', 'App\Http\Controllers\ClientController@fsubmit');
Route::get('searchsubmited/{id}', 'App\Http\Controllers\ClientController@searchSubmitted');
Route::get('rejected', 'App\Http\Controllers\ClientController@rejected');
Route::put('reject/{id}', 'App\Http\Controllers\ClientController@reject');
Route::put('aprove/{id}', 'App\Http\Controllers\ClientController@aprove');
Route::get('transactions/{id}', 'App\Http\Controllers\ClientController@loantransactions');
Route::get('defaulted/{id}', 'App\Http\Controllers\ClientController@defaulted');
Route::get('submited/{id}', 'App\Http\Controllers\ClientController@submitted');
Route::get('addclientstoerp', 'App\Http\Controllers\ClientController@addclientstoerp');
Route::get('verify/{id}', 'App\Http\Controllers\ClientController@checkiftransidexists');
Route::post('recover', 'App\Http\Controllers\IntegrationController@recover');
Route::post('exitclient', 'App\Http\Controllers\IntegrationController@exitclient');
Route::post('testhook', 'App\Http\Controllers\IntegrationController@testhook');




// {\n	\"CustId\": \""+ loan.getClientId()+"\ , loan_id
Route::post('disbursed', 'App\Http\Controllers\OverpaymentController@inittransferfromsavings');
// {\n	\"CustId\": \""+ loan.getClientId()+"\",\n	\"loanid\": \""+loan.getId()+"\",\n	\"TransDate\": \""+transactionDate+"\",\n	\"Amount\": \""+overpaymentt+"\"\n}
Route::post('overpayment', 'App\Http\Controllers\OverpaymentController@initsendtosavings');




