<?php

use App\Http\Controllers\DraftController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\JointController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\PresidentialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepublicController;
use Illuminate\Support\Facades\Route;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/latest_issuances', [IssuanceController::class, 'index']);
    Route::post('/latest_issuances', [IssuanceController::class, 'store'])->name('latest.store');

    Route::get('/joint_circulars', [JointController::class, 'index']);
    Route::post('/joint_circulars', [JointController::class, 'store'])->name('joint.store');
    Route::get('/joint_circulars/edit/{joint}', [JointController::class, 'edit'])->name('joint.edit');
    Route::put('/joint_circulars/{joint}', [JointController::class, 'update'])->name('joint.update');
    Route::delete('/joint_circulars/{joint}', [JointController::class, 'destroy'])->name('joint.delete');

    Route::get('/memo_circulars', [MemoController::class, 'index']);

    Route::get('/presidential_directives', [PresidentialController::class, 'index']);

    Route::get('/draft_issuances', [DraftController::class, 'index']);

    Route::get('/republic_acts', [RepublicController::class, 'index']);

    Route::get('/legal_opinions', [LegalController::class, 'index']);

});

require __DIR__.'/auth.php';
