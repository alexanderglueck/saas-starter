<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PageController::class, 'welcome']);

Auth::routes(['verify' => true]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/setup', [SetupController::class, 'show'])->name('setup');

    Route::get('session', [SessionController::class, 'index'])->name('session.index');
    Route::delete('session', [SessionController::class, 'destroy'])->name('session.destroy');

    Route::get('two-factor/create', [TwoFactorController::class, 'create'])->name('two-factor.create');
    Route::post('two-factor', [TwoFactorController::class, 'store'])->name('two-factor.store');
    Route::get('two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::delete('two-factor', [TwoFactorController::class, 'destroy'])->name('two-factor.destroy');
});

Route::resource('role', 'RoleController');

Route::resource('plan', 'PlanController')->only('index');
