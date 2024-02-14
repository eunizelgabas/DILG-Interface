<?php

use App\Http\Controllers\DraftController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\JointController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\PresidentialController;
use App\Http\Controllers\RepublicController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    Route::post('/logout', UserController::class, 'logout');
});

Route::post('/auth/login', [UserController::class, 'login']);

// Route::get('/test', function() {
//     return response([
//         'message' => 'Api is working'
//     ], 200);
// });

Route::get('/latest_issuances', [IssuanceController::class, 'index']);
Route::get('/joint_circulars', [JointController::class, 'index']);
Route::get('/legal_opinions', [LegalController::class, 'index']);
Route::get('/memo_circulars', [MemoController::class, 'index']);
Route::get('/presidential_directives', [PresidentialController::class, 'index']);
Route::get('/draft_issuances', [DraftController::class, 'index']);
Route::get('/republic_acts', [RepublicController::class, 'index']);

Route::get('/user',[UserController::class, 'apiIndex']);
Route::put('/user/update', [UserController::class, 'update']);

// Route::get('/legal_opinions', [LegalController::class, 'index']);
// Route::get('/api/download_issuance/{issuance}', [IssuanceController::class, 'downloadIssuanceFile']);
