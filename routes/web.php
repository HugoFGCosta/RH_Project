<?php

use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\AuthenticatedRegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkShiftController;

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
Route::post('/admin-register', [AdminRegisterController::class, 'create'])->name('admin-register');
Route::get('/admin-register', [AdminRegisterController::class, 'showRegisterForm']);

Route::get('/user/edit', [UserController::class, 'edit']);
Route::put('/user/edit', [UserController::class, 'update']);
Route::get('/user/show', [UserController::class, 'show']);
Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);


Route::get('/users/show-all', [UserController::class, 'showAll']);

Route::get('/user/edit/{id}', [UserController::class, 'editSpec']);
Route::put('/user/edit/{id}', [UserController::class, 'updateSpec']);
Route::get('/user/show/{id}', [UserController::class, 'showSpec']);


/*Rotas WorkShifts*/

Route::resource('work-shifts', \App\Http\Controllers\WorkShiftController::class);
Route::get('/work-shifts/create', [WorkShiftController::class, 'create'])->name('work-shifts.create');
Route::post('/work-shifts', [WorkShiftController::class, 'store']);
Route::get('/work-shifts/show', [WorkShiftController::class, 'show']);
Route::get('/work-shifts/edit/{work_shift}', [WorkShiftController::class, 'edit']);
Route::put('/work-shifts/{work_shift}', [WorkShiftController::class, 'update']);


/*Rotas Import Export*/

/* ROTA PRESENÇA */

Route::post('user/presence/storeSimulated', [PresenceController::class, 'storeSimulated']); //ROTA SIMULADA
/*Route::post('/user/presence', [PresenceController::class, 'presence']);*/
Route::post('/user/presence/store', [PresenceController::class, 'store']);
/*Route::get('/user/presence', [PresenceController::class, 'getPresence']);*/
Route::get('/user/presence/status', [PresenceController::class, 'getStatus']);



/* ROTAS IMPORT / EXPORT */

Route::post('import', [\App\Http\Controllers\UserController::class, 'import'])->name('import');
Route::get('export', [\App\Http\Controllers\UserController::class, 'export'])->name('export');

Route::post('import/absence', [\App\Http\Controllers\AbsenceController::class, 'import'])->name('importAbsences');
Route::get('export/absence', [\App\Http\Controllers\AbsenceController::class, 'export'])->name('exportAbsences');

Route::post('import/vacations', [\App\Http\Controllers\VacationController::class, 'import'])->name('importVacations');
Route::get('export/vacations', [\App\Http\Controllers\VacationController::class, 'export'])->name('exportVacations');

Route::post('import/presences', [\App\Http\Controllers\PresenceController::class, 'import'])->name('importPresences');
Route::get('export/presences', [\App\Http\Controllers\PresenceController::class, 'export'])->name('exportPresences');


/* Rota CALENDARIO */
Route::controller(EventController::class)->group(function () {
    Route::get('fullcalender', 'index');
    Route::post('fullcalenderAjax', 'ajax');
})->middleware('check.calendar');

/* Rotas Absences */
Route::get('users/{user}/absences', [\App\Http\Controllers\AbsenceController::class, 'absencesByUser']);






Route::get('export/work-shifts', [\App\Http\Controllers\WorkShiftController::class, 'export'])->name('exportWorkShifts');
Route::get('export/work-shifts/{user}', [\App\Http\Controllers\WorkShiftController::class, 'exportUserWorkShift'])->name('exportUserWorkShift');
