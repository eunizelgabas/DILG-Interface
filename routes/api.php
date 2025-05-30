<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\JointController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\PresidentialController;
use App\Http\Controllers\RepublicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\LegalOpinionController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
//     Route::post('/logout', UserController::class, 'logout');
// });


//LEGAL OPINIONS FROM DILG BOHOL
// Route::post('/legal_opinions', [LegalOpinionController::class, 'sendToDilg']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']); // Endpoint to fetch user data
    Route::post('/logout', [UserController::class, 'logout']); // Endpoint to log out
});
Route::post('/auth/login', [UserController::class, 'login']);

Route::get('/latest_issuances', [IssuanceController::class, 'index']);
Route::get('/joint_circulars', [JointController::class, 'indexMobile']);
Route::get('/legal_opinions', [LegalController::class, 'getLegalOpinionsJson']);
Route::get('/memo_circulars', [MemoController::class, 'index']);
Route::get('/presidential_directives', [PresidentialController::class, 'indexMobile']);
Route::get('/draft_issuances', [DraftController::class, 'index']);
Route::get('/republic_acts', [RepublicController::class, 'indexMobile']);
Route::get('/recent-issuances', [IssuanceController::class, 'recent']);
// Route::get('/new-issuances-count', [IssuanceController::class, 'getNewIssuancesCount']);

// Route::get('/user',[UserController::class, 'apiIndex']);
// Route::put('/user/update', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->put('/user/update/{user}', [UserController::class, 'update']);
Route::get('/auth/validate-token', [UserController::class, 'validateToken']);
Route::get('/user/{user}', [UserController::class, 'getUserDetails']);

Route::get('/images/{filename}', [UserController::class, 'avatar'])->name('image.get');
Route::get('/{filename}', [UserController::class, 'getAvatar'])
    ->middleware('auth');

Route::middleware('auth:sanctum')->put('/users/{user}/change-password', [UserController::class, 'changePassword']);

Route::post('/visitor/count', [VisitorController::class, 'increment']);

