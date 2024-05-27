<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VacationController;
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
Route::get('/import-export-data', [App\Http\Controllers\ButtonController::class, 'importExportData'])->name('importExportData');
Route::get('/daily-tasks', [App\Http\Controllers\ButtonController::class, 'dailyTasks']);
Route::get('/requests', [App\Http\Controllers\ButtonController::class, 'requests']);
Route::get('/settings', [App\Http\Controllers\ButtonController::class, 'settings']);

/*Rotas Users*/
Route::get('/users/create', [UserController::class, 'create']);
Route::get('/user/edit', [UserController::class, 'edit']);
Route::put('/user/edit', [UserController::class, 'update']);
Route::get('/user/show', [UserController::class, 'show']);
Route::post('/user/presence', [UserController::class, 'presence']);
Route::post('/user/presence/store', [UserController::class, 'store']);
Route::get('/user/presence', [UserController::class, 'getPresence']);

//rotas vacation

Route::get('/vacation', [VacationController::class, 'index'])->name('vacations.index');


Route::get('/vacation', [VacationController::class, 'index'])->name('vacations.index');

Route::get('/vacations', [VacationController::class, 'index'])->name('vacations.index');
Route::get('/vacations/create', [VacationController::class, 'create'])->name('vacations.create');
Route::post('/vacations', [VacationController::class, 'store'])->name('vacations.store');
Route::get('/vacations/{vacation}', [VacationController::class, 'show'])->name('vacations.show');
Route::get('/vacations/edit/{vacation}', [VacationController::class, 'edit'])->name('vacations.edit');
Route::put('/vacations/edit/{vacation}', [VacationController::class, 'update'])->name('vacations.update');
Route::delete('/vacations/delete/{vacation}', [VacationController::class, 'destroy'])->name('vacations.destroy');
//check for val on ed aft the creation only for admin

Route::post('user/presence/storeSimulated', [UserController::class, 'storeSimulated']); //ROTA SIMULADA

Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);


/*Rotas Export*/
Route::get('users/export/', [\App\Http\Controllers\UserController::class, 'export'])->name('exportUsers');
Route::get('absences/export/', [\App\Http\Controllers\AbsenceController::class, 'export'])->name('exportAbsences');
Route::get('vacations/export/', [\App\Http\Controllers\VacationController::class, 'export'])->name('exportVacations');
Route::get('presences/export/', [\App\Http\Controllers\PresenceController::class, 'export'])->name('exportPresences');


/*Rotas Import*/
Route::post('users/import/', [\App\Http\Controllers\UserController::class, 'import'])->name('importUsers');
Route::post('absences/import/', [\App\Http\Controllers\AbsenceController::class, 'import'])->name('importAbsences');
Route::post('vacations/import/', [\App\Http\Controllers\VacationController::class, 'import'])->name('importVacations');
Route::post('presences/import/', [\App\Http\Controllers\PresenceController::class, 'import'])->name('importPresences');
