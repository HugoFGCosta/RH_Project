<?php

use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\AuthenticatedRegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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
    return view('auth.login');
});

Auth::routes();
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->middleware('auth');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*Rotas Menu*/
Route::get('/menu', [App\Http\Controllers\ButtonController::class, 'index']);
Route::get('/register-schedule', [App\Http\Controllers\ButtonController::class, 'registerSchedule']);
Route::get('/dashboard-statistics', [App\Http\Controllers\ButtonController::class, 'dashboardStatistics']);
Route::get('/view-absences', [App\Http\Controllers\ButtonController::class, 'viewAbsences']);
Route::get('/manage-data', [App\Http\Controllers\ButtonController::class, 'manageData']);
Route::get('/vacation-plans', [App\Http\Controllers\ButtonController::class, 'vacationPlans']);
Route::get('/approve-absence', [App\Http\Controllers\ButtonController::class, 'approveAbsences']);
Route::get('/import-export-data', [App\Http\Controllers\ButtonController::class, 'importExportData']);
Route::get('/daily-tasks', [App\Http\Controllers\ButtonController::class, 'dailyTasks']);
Route::get('/requests', [App\Http\Controllers\ButtonController::class, 'requests']);
Route::get('/settings', [App\Http\Controllers\ButtonController::class, 'settings']);

/*Rotas Users*/
Route::post('/admin-register', [AdminRegisterController::class, 'create'])->name('admin-register');
Route::get('/admin-register', [AdminRegisterController::class, 'showRegisterForm']);



Route::get('/users/create', [UserController::class, 'create']);


Route::get('/user/edit', [UserController::class, 'edit']);
Route::put('/user/edit', [UserController::class, 'update']);
Route::get('/user/show', [UserController::class, 'show']);
Route::post('/user/presence', [UserController::class, 'presence']);
Route::post('/user/presence/store', [UserController::class, 'store']);
Route::get('/user/presence', [UserController::class, 'getPresence']);

Route::post('user/presence/storeSimulated', [UserController::class, 'storeSimulated']); //ROTA SIMULADA