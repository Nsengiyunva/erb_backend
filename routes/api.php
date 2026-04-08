<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EngineersController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\OtherController;
use App\Http\Controllers\FileController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([ 'middleware' => [ 'api', 'cors' ],'prefix' => 'auth' ], function ($router) {
  // NEC

  // Route::get('/', 'RequestController@test');
  //   Route::post( '/getMaxMemoId', 'RequestController@getAllMemoIds' );
  //   Route::post( 'login', 'AuthController@login');
  //   Route::post( 'addUser', 'AuthController@register');
    

  //   Route::get( 'getNewStaff', 'RequestController@getNewStaff' );
  //   Route::post( 'updateUser', 'RequestController@updateUser' );
  //   Route::post( 'storeISO', 'DocumentFileController@storeISO' );
  //   Route::post( 'getISODocument', 'DocumentFileController@downloadISO');
  //   Route::get( 'isoAll', 'DocumentFileController@ISOAll');
  //   Route::post( 'storeMemo', 'DocumentFileController@storeMemo' );
  //   Route::get( 'memoAll', 'DocumentFileController@memoAll' );
  //   Route::post( 'approve-memo', 'DocumentFileController@approveMemo');
  //   Route::post( 'shareMemo', 'DocumentFileController@shareMemo');
  //   Route::get( 'file/country_list', 'FileController@countryList' );
  //   Route::get( 'getStaffFiles/{id}', 'DocumentFileController@getStaffFiles' );
  //   Route::get( 'getAllUsers', 'RequestController@getAllUsers' );
  //   Route::post( 'loginUser', 'RequestController@loginUser' );
  //   Route::post( 'logout', 'AuthController@logout');
  //   Route::post( 'updatePassword', 'RequestController@updatePassword');
  //   Route::post( 'refresh', 'AuthController@refresh');
  //   Route::post( 'deleteFile', 'RequestController@deleteStaffFile' );

  //   Route::post( 'submitRequisition', 'RequestController@store');
  //   Route::post('updateRequisition', 'RequestController@updateRequisition');
  //   Route::post( 'getAll', 'RequestController@getAll');
  //   Route::post('addDocuments', 'DocumentFileController@store');
  //   Route::post('updateRequest', 'RequestController@updateItem');
  //   Route::post('updateCashierVoucher', 'RequestController@updateCashierVoucher');
  //   Route::post('getPaymentVoucher', 'RequestController@getPaymentVoucher');

  //   Route::post('saveItems', 'RequestController@saveItems');
  //   Route::post( 'getProcurementItems', 'RequestController@getProcurementItems' );
  //   Route::post( 'getRole', 'RequestController@getRole' );
  //   Route::get( 'getApproved', 'RequestController@approved' );

  //   Route::post( 'submitLeaveApplication', 'LeaveApplicationController@store');
  //   Route::get( 'getAllApplications', 'LeaveApplicationController@getAll');
  //   Route::post('updateLeave', 'LeaveApplicationController@updateLeave');

  //   Route::post( 'updateRecord', 'LeaveApplicationController@updateRecord');
  //   Route::post( 'fetchUserFiles', 'DocumentFileController@fetchUserFiles' );

  //   Route::post( 'add_payroll', 'DocumentFileController@add_payroll' );
  //   Route::get( 'get_all_payrolls', 'DocumentFileController@get_all_payrolls' );
  //   Route::post( 'update_payroll', 'DocumentFileController@updateRecord');
  //   Route::post( 'updateProcurementRequest', 'DocumentFileController@updateProcurementRequest');

  //   Route::post( 'add_remark', 'RequestController@add_remark' );
  //   Route::post('getUserRemarks', 'RequestController@getRemarks');
  //   Route::post('getOfficerRemarks', 'RequestController@getOfficerRemarks');
  //   Route::post('getUserByEmail', 'RequestController@getUserByEmail');

    // NEC
  
  Route::post( "erb_loginUser", [EngineersController::class, 'loginUser'] );

  //register user
  Route::post( "register-user", [EngineersController::class, 'storeUser'] );

  Route::get( "getAllUsers", [EngineersController::class, 'getAllUsers'] );
  Route::post( "erb_pay", [EngineersController::class, 'preparePayment'] );
  Route::get(  "erb_getLicences", [EngineersController::class, 'getLicences'] );
  Route::get( "erb_account-licenses/{user_id}", [EngineersController::class, 'fetchAccountLicenses']  );
  Route::get(  "erb_getLicenceById/{id}", [EngineersController::class, 'getLicenceById'] );
  Route::get(  "erb_getRemarks/{licenceId}", [EngineersController::class, 'getLicenceRemarks'] );
  Route::post( "erb_update", [EngineersController::class, 'updateLicence'] );
  Route::post( "erb_update_accounts", [EngineersController::class, 'updateLicencePayment']);
  Route::post( "erb-success-payment", [EngineersController::class, 'paymentSuccess'] );
  Route::get( "erb-getSponsors", [EngineersController::class, 'getSponsorsByUser'] );
  Route::post( "erb_updateSponsor", [EngineersController::class, 'updateSponsor'] );
  Route::get( "erb_engineers/{category}", [EngineersController::class,'fetchErbEngineers'] ); 
  Route::get( "erb_registered", [EngineersController::class, 'fetchEngineers'] );
  Route::get("/payments/{id}",[PaymentsController::class, 'show'] );
  Route::get("/payments",[PaymentsController::class, 'fetchAllPayments'] );
  Route::post( "send-email-notification", [ EngineersController::class, 'sendmail'] );
  Route::get( "check-licenses/{user_id}", [ EngineersController::class, 'checkLicenses' ] );
  
  Route::post('send-otp', [ OTPController::class, 'sendOTP' ]);
  Route::post('verify-otp', [ OTPController::class, 'verifyOTP' ]);

  Route::post( "upload", [FileController::class, "upload" ] );

  Route::post( "save-draft", [EngineersController::class, "storeDraft" ] );
  Route::get( "get-drafts/{email}", [EngineersController::class, "fetchDraftsByEmail"] );
  Route::post( "fetch-drafts-by-id", [EngineersController::class, "getDraftsById" ] );
  Route::put( "update-draft", [EngineersController::class, "updateDraft" ] );

  Route::get( "get-registered-engineers", [ EngineerController::class, "getRegisteredEngineers" ] );
  Route::post( "is-engineer-authenticated", [ EngineerController::class, "isEngineerAuthenticated" ] );
  Route::post( "verify-license/{license_no}", [ EngineerController::class, "verifyLicense" ] );
  
  //submit an application
  // Router::get( "get-registered-engineers", [ EngineerController::class, "getRegisteredEngineers" ] );

  //generate certificate
  // Router::get( "get-registered-engineers", [ EngineerController::class, "getRegisteredEngineers" ] );
} );

Route::post("/payments/callback",[PaymentsController::class, 'callback'] )->name("payments.callback");









