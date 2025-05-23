<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\JointController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\PresidentialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepublicActController;
use App\Http\Controllers\RepublicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;
// use PDO;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::post('webhook/republic-act', [RepublicActController::class, 'receiveRepublicAct']);
Route::post('webhook/presidential-directive', [PresidentialController::class, 'receivePresidentialDirectives']);
Route::post('webhook/joint-circular', [JointController::class, 'receiveJointCirculars']);
Route::post('webhook/legal-opinion', [LegalController::class, 'receiveLegalOpinion']);

Route::middleware('auth')->group(function () {

    //Route for dashboard - Eula
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    //Route for User - Eula
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/users', [UserController::class, 'store'])->name('user.store');
    Route::get('/users/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::get('/users/show/{user}', [UserController::class, 'show'])->name('user.show');
    Route::put('/users/{user}', [UserController::class, 'updateNotApi'])->name('user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.delete');

    Route::put('/users/activate/{user}', [UserController::class, 'activate'])->name('user.activate');
    Route::put('/users/deactivate/{user}', [UserController::class, 'deactivate'])->name('user.deactivate');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Route for latest Issuances - Eunizel
    Route::get('/latest_issuances', [IssuanceController::class, 'index'])->name('latest.index');
    Route::post('/latest_issuances', [IssuanceController::class, 'store'])->name('latest.store');
    Route::get('/latest_issuances/edit/{latest}', [IssuanceController::class, 'edit'])->name('latest.edit');
    Route::put('/latest_issuances/{latest}', [IssuanceController::class, 'update'])->name('latest.update');
    Route::delete('/latest_issuances/{latest}', [IssuanceController::class, 'destroy'])->name('latest.delete');

    //Route for Joint Circulars - Eunizel
    Route::get('/joint_circulars', [JointController::class, 'index'])->name('joint.index');
    // Route::post('/joint_circulars', [JointController::class, 'store'])->name('joint.store');
    // Route::get('/joint_circulars/edit/{joint}', [JointController::class, 'edit'])->name('joint.edit');
    // Route::put('/joint_circulars/{joint}', [JointController::class, 'update'])->name('joint.update');
    // Route::delete('/joint_circulars/{joint}', [JointController::class, 'destroy'])->name('joint.delete');

    //Route for Memo Circulars - Eunizel
    Route::get('/memo_circulars', [MemoController::class, 'index'])->name('memo.index');
    Route::post('/memo_circulars', [MemoController::class, 'store'])->name('memo.store');
    Route::get('/memo_circulars/edit/{memo}', [MemoController::class, 'edit'])->name('memo.edit');
    Route::put('/memo_circulars/{memo}', [MemoController::class, 'update'])->name('memo.update');
    Route::delete('/memo_circulars/{memo}', [MemoController::class, 'destroy'])->name('memo.delete');

    //Route for Presidential Directives - Eunizel
    Route::get('/presidential_directives', [PresidentialController::class, 'index'])->name('presidential.index');
    // Route::post('/presidential_directives', [PresidentialController::class, 'store'])->name('presidential.store');
    // Route::get('/presidential_directives/edit/{presidential}', [PresidentialController::class, 'edit'])->name('presidential.edit');
    // Route::put('/presidential_directives/{presidential}', [PresidentialController::class, 'update'])->name('presidential.update');
    // Route::delete('/presidential_directives/{presidential}', [PresidentialController::class, 'destroy'])->name('presidential.delete');

    //Route for Draft Issuance- Eunizel
    Route::get('/draft_issuances', [DraftController::class, 'index'])->name('draft.index');
    Route::post('/draft_issuances', [DraftController::class, 'store'])->name('draft.store');
    Route::get('/draft_issuances/edit/{draft}', [DraftController::class, 'edit'])->name('draft.edit');
    Route::put('/draft_issuances/{draft}', [DraftController::class, 'update'])->name('draft.update');
    Route::delete('/draft_issuances/{draft}', [DraftController::class, 'destroy'])->name('draft.delete');

    //Route for Republic Acts- Eunizel
    Route::get('/republic_acts', [RepublicActController::class, 'index'])->name('republic.index');
    // Route::post('/republic_acts', [RepublicController::class, 'store'])->name('republic.store');
    // Route::get('/republic_acts/edit/{republic}', [RepublicController::class, 'edit'])->name('republic.edit');
    // Route::put('/republic_acts/{republic}', [RepublicController::class, 'update'])->name('republic.update');
    // Route::delete('/republic_acts/{republic}', [RepublicController::class, 'destroy'])->name('republic.delete');

    //Route for Legal Opinion- Eunizel
    // Route::post('webhook/legal-opinion', [LegalController::class, 'receiveLegalOpinion']);

    // Route::get('/pdfs', function() {
    //     $pdfs = \App\Models\LegalOpinionPdf::all();
    //     return response()-> json($pdfs); 
    // });
    Route::get('/check-pdfs', function () {
        $pdfs = \App\Models\LegalOpinionPdf::all();
        return response()->json($pdfs);
    });

    // Route::get('/legal_opinions', [LegalController::class, 'show'])->name('legal.index');
    Route::get('/legal_opinions', [LegalController::class, 'index'])->name('legal.index');
    Route::get('/legal_opinion/{id}', [LegalController::class, 'show'])->name('legal.show');
    Route::post('/legal_opinions', [LegalController::class, 'store'])->name('legal.store');
    Route::get('/legal_opinions/edit/{legal}', [LegalController::class, 'edit'])->name('legal.edit');
    Route::put('/legal_opinions/{legal}', [LegalController::class, 'update'])->name('legal.update');
    Route::delete('/legal_opinions/{legal}', [LegalController::class, 'destroy'])->name('legal.delete');

    //Route for Logs - Eula
    Route::get('logs', [LogController::class, 'index'])->name('log.index');

    //Daily visitor User count
    Route::get('/visitors', [VisitorController::class, 'show']);

});

require __DIR__ . '/auth.php';
