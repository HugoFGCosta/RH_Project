<?php

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*Rotas Menu*/
Route::get('/menu', [App\Http\Controllers\ButtonController::class, 'index']);
Route::get('/register-schedule', [App\Http\Controllers\ButtonController::class, 'registerSchedule']);
Route::get('/dashboard-statistics', [App\Http\Controllers\ButtonController::class, 'dashboardStatistics']);
Route::get('/view-absences', [App\Http\Controllers\ButtonController::class, 'viewAbsences']);
Route::get('/manage-data', [App\Http\Controllers\ButtonController::class, 'manageData']);
Route::get('/vacation-plans', [App\Http\Controllers\ButtonController::class, 'vacationPlans']);
Route::get('/approve-absence', [App\Http\Controllers\ButtonController::class, 'approveAbsences']);
Route::get('/import-export-data', [App\Http\Controllers\ButtonController::class, 'importExportData'])->name('importExportData');

/*Rotas Users*/
Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create']);
Route::get('/user/edit', [App\Http\Controllers\UserController::class, 'edit']);
Route::put('/user/edit', [App\Http\Controllers\UserController::class, 'update']);
Route::get('/user/show', [App\Http\Controllers\UserController::class, 'show']);


Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);


/*Rotas Export*/
Route::get('export-csv', [\App\Http\Controllers\UserController::class, 'exportCSVUsers'])->name('exportUsers');
Route::get('exportCSVAbsences', [\App\Http\Controllers\AbsenceController::class, 'exportCSVAbsences'])->name('exportAbsences');
Route::get('exportCSVVacations', [\App\Http\Controllers\VacationController::class, 'exportCSVVacations'])->name('exportVacations');
Route::get('exportCSVPresences', [\App\Http\Controllers\PresenceController::class, 'exportCSVPresences'])->name('exportPresences');

Route::post('import', [\App\Http\Controllers\UserController::class, 'importCSV'])->name('import');

