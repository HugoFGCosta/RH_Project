<?php

use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserShiftController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkShiftController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkTimeController;



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

/*Rotas Menu ->  IMPLEMENTADO MIDDLEWARE*/
Route::get('/menu', [App\Http\Controllers\ButtonController::class, 'index']); // menu
Route::get('/register-schedule', [App\Http\Controllers\ButtonController::class, 'registerSchedule']);
Route::get('/dashboard-statistics', [App\Http\Controllers\ButtonController::class, 'dashboardStatistics']); // dashboard das horas extras
Route::get('/view-absences', [App\Http\Controllers\ButtonController::class, 'viewAbsences']);
Route::get('/manage-data', [App\Http\Controllers\ButtonController::class, 'manageData']);
Route::get('/approve-absence', [App\Http\Controllers\ButtonController::class, 'approveAbsences'])->middleware('AdminOrManagerMiddleware'); // Aprovar faltas, ADMIN e Gestor
Route::get('/vacation', [App\Http\Controllers\ButtonController::class, 'vacationPlans']);
Route::get('/import-export-data', [App\Http\Controllers\ButtonController::class, 'importExportData'])->name('importExportData')->middleware('AdminMiddleware'); // APENAS ADMIN E GESTOR
Route::get('/daily-tasks', [App\Http\Controllers\ButtonController::class, 'dailyTasks']);
Route::get('/requests', [App\Http\Controllers\ButtonController::class, 'requests']);
Route::get('/settings', [App\Http\Controllers\ButtonController::class, 'settings']);
Route::get('/attendance-record', [App\Http\Controllers\ButtonController::class, 'attendanceRecord']); // registro de assiduidade


/*Rotas Users  ->  IMPLEMENTADO MIDDLEWARE*/
Route::post('/admin-register', [AdminRegisterController::class, 'create'])->name('admin-register')->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota
Route::get('/admin-register', [AdminRegisterController::class, 'showRegisterForm'])->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota

Route::get('/user/edit', [UserController::class, 'edit']);
Route::put('/user/edit', [UserController::class, 'update']);
Route::get('/user/show', [UserController::class, 'show']);
Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);


Route::get('/users/show-all', [UserController::class, 'showAll'])->middleware('AdminOrManagerMiddleware'); // LISTA DE TODOS, apenas Admin e Gestor tem acesso

Route::get('/user/edit/{id}', [UserController::class, 'editSpec'])->middleware('AdminOrManagerMiddleware'); // Edit funcionario especifico, apenas Admin e Gestor tem acesso;
Route::put('/user/edit/{id}', [UserController::class, 'updateSpec'])->middleware('AdminOrManagerMiddleware'); // Edit funcionario especifico, apenas Admin e Gestor tem acesso;
Route::get('/user/show/{id}', [UserController::class, 'showSpec'])->middleware('AdminOrManagerMiddleware'); // Edit funcionario especifico, apenas Admin e Gestor tem acesso;


/*Rotas WorkShifts  ->  IMPLEMENTADO MIDDLEWARE*/

Route::resource('work-shifts', WorkShiftController::class)->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota para criar um turno
Route::get('/work-shifts/create', [WorkShiftController::class, 'create'])->name('work-shifts.create')->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota para criar um turno
Route::post('/work-shifts', [WorkShiftController::class, 'store'])->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota para criar um turno
Route::get('/work-shifts/show', [WorkShiftController::class, 'show'])->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota para criar um turno
Route::get('/work-shifts/edit/{work_shift}', [WorkShiftController::class, 'edit'])->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota para criar um turno
Route::put('/work-shifts/{work_shift}', [WorkShiftController::class, 'update'])->middleware('AdminMiddleware'); // APENAS ADMIN pode acessar a rota para criar um turno


//rotas vacation   ->  IMPLEMENTADO MIDDLEWARE

Route::get('/vacation', [VacationController::class, 'index'])->name('vacations.index');
Route::get('/vacations/create', [VacationController::class, 'create'])->name('vacations.create');
Route::post('/vacations', [VacationController::class, 'store'])->name('vacations.store');
Route::get('/vacations/{vacation}', [VacationController::class, 'show'])->name('vacations.show');
Route::get('/vacations/edit/{vacation}', [VacationController::class, 'edit'])->name('vacations.edit')->middleware('AdminMiddleware'); // Apenas ADMIN aprovar ferias
Route::put('/vacations/{vacation}', [VacationController::class, 'update'])->name('vacations.update')->middleware('AdminMiddleware'); // Apenas ADMIN aprovar ferias
Route::delete('/vacations/delete/{vacation}', [VacationController::class, 'destroy'])->name('vacations.destroy')->middleware('AdminMiddleware'); // Apenas ADMIN aprovar ferias

/*Rotas Import Export*/

/* ROTA PRESENÇA   ->  AINDA NAO IMPLEMENTADO MIDDLEWARE */

Route::post('/user/presence/store', [PresenceController::class, 'store']); /* <<<<<<<<<<< ESSA ROTA  */
Route::get('/user/presence/status', [PresenceController::class, 'getStatus']);
//Route::post('user/presence/storeSimulated', [PresenceController::class, 'storeSimulated']); //ROTA SIMULADA
/*Route::post('/user/presence', [PresenceController::class, 'presence']);*/
/*Route::get('/user/presence', [PresenceController::class, 'getPresence']);*/



/* ROTAS IMPORT / EXPORT    ->  IMPLEMENTADO MIDDLEWARE APENAS ADMIN*/

Route::post('import', [\App\Http\Controllers\UserController::class, 'import'])->name('import')->middleware('AdminMiddleware');
Route::get('export', [\App\Http\Controllers\UserController::class, 'export'])->name('export')->middleware('AdminMiddleware');

Route::post('import/absence', [\App\Http\Controllers\AbsenceController::class, 'import'])->name('importAbsences')->middleware('AdminMiddleware');
Route::get('export/absence', [\App\Http\Controllers\AbsenceController::class, 'export'])->name('exportAbsences')->middleware('AdminMiddleware');

Route::post('import/vacations', [\App\Http\Controllers\VacationController::class, 'import'])->name('importVacations')->middleware('AdminMiddleware');
Route::get('export/vacations', [\App\Http\Controllers\VacationController::class, 'export'])->name('exportVacations')->middleware('AdminMiddleware');

Route::post('import/presences', [\App\Http\Controllers\PresenceController::class, 'import'])->name('importPresences')->middleware('AdminMiddleware');
Route::get('export/presences', [\App\Http\Controllers\PresenceController::class, 'export'])->name('exportPresences')->middleware('AdminMiddleware');

Route::get('export/work-shifts', [\App\Http\Controllers\WorkShiftController::class, 'export'])->name('exportWorkShifts')->middleware('AdminMiddleware');
Route::get('export/work-shifts/{user}', [\App\Http\Controllers\WorkShiftController::class, 'exportUserWorkShift'])->name('exportUserWorkShift')->middleware('AdminMiddleware');
Route::get('export/work-shifts/{user}', [\App\Http\Controllers\WorkShiftController::class, 'exportUserWorkShift'])->name('exportUserWorkShift')->middleware('AdminMiddleware');


/* Rota CALENDARIO*/
Route::controller(EventController::class)->group(function () {
    Route::get('fullcalender', 'index');
    Route::post('fullcalenderAjax', 'ajax');
})->middleware('check.calendar');

/* Rotas Absences   ->  AINDA NAO IMPLEMENTADO MIDDLEWARE*/
Route::get('users/{user}/absences', [\App\Http\Controllers\AbsenceController::class, 'absencesByUser']);
Route::get('/approve-absence', [App\Http\Controllers\ButtonController::class, 'approveAbsences']);

/* Rotas Justificações */
//Route::get('absences/{absence}/justification/create', [\App\Http\Controllers\JustificationController::class, 'create']);
Route::get('/justification/create', [\App\Http\Controllers\JustificationController::class, 'create']);
Route::get('absences', [\App\Http\Controllers\AbsenceController::class, 'index']);
Route::resource('justifications', \App\Http\Controllers\JustificationController::class);
//Route::post('absences/{absence}/justification', [\App\Http\Controllers\JustificationController::class, 'store']);
Route::post('absences/justification/store', [\App\Http\Controllers\JustificationController::class, 'store']);
Route::get('/justifications/show', [\App\Http\Controllers\JustificationController::class, 'show']);
Route::get('/justifications/edit/{justifications}', [\App\Http\Controllers\JustificationController::class, 'edit']);
Route::put('/justifications/{justifications}', [\App\Http\Controllers\JustificationController::class, 'update']);
Route::get('/justification/{justification}/manage', [\App\Http\Controllers\JustificationController::class, 'justificationManage']);
Route::get('/justification/{justification}/reject', [\App\Http\Controllers\JustificationController::class, 'justificationReject']);
Route::get('/justification/{justification}/approve', [\App\Http\Controllers\JustificationController::class, 'justificationApprove']);
Route::get('/pending-justifications', [\App\Http\Controllers\JustificationController::class, 'pendingJustifications']);
Route::get('/justification/{absence}/download', [\App\Http\Controllers\JustificationController::class, 'justificationDownload']);

/* Rotas Turnos   ->  IMPLEMENTADO MIDDLEWARE ADMIN/MANAGER*/
Route::get('users/shift-list', [UserShiftController::class, 'show'])->middleware('AdminOrManagerMiddleware'); // LISTA DE TODOS, apenas Admin e Gestor tem acesso
Route::get('user/shifts', [UserShiftController::class, 'show_spec'])->middleware('AdminOrManagerMiddleware'); // Apenas Admin e Gestor tem acesso


Route::get('users/shift-list/edit/{user_shift}', [UserShiftController::class, 'edit'])->name('user_shift.edit')->middleware('AdminOrManagerMiddleware'); // Apenas Admin e Gestor tem acesso
Route::put('users/shift-list/edit/{user_shift}', [UserShiftController::class, 'update'])->name('user_shift.update')->middleware('AdminOrManagerMiddleware'); // Apenas Admin e Gestor tem acesso
Route::delete('users/shift-list/{user_shift}', [UserShiftController::class, 'destroy'])->name('user_shift.destroy')->middleware('AdminOrManagerMiddleware'); // Apenas Admin e Gestor tem acesso


/* Rotas export   ->  AINDA NAO IMPLEMENTADO MIDDLEWARE*/
/* Route::get('export/work-shifts', [\App\Http\Controllers\WorkShiftController::class, 'export'])->name('exportWorkShifts');
Route::get('export/work-shifts/{user}', [\App\Http\Controllers\WorkShiftController::class, 'exportUserWorkShift'])->name('exportUserWorkShift');
Route::get('export/work-shifts/{user}', [\App\Http\Controllers\WorkShiftController::class, 'exportUserWorkShift'])->name('exportUserWorkShift'); */



/*Rotas estatisticas*/
Route::get('/dashboard-statistics', [DashboardController::class, 'statistics'])->name('dashboard.statistics');
Route::post('/dashboard-statistics/filter', [DashboardController::class, 'filterStatistics'])->name('dashboard.filter');

/* Rota Saldo */
Route::get('/time-bank-balance', [\App\Http\Controllers\BankHourController::class, 'index']);

/*Rotas de gestão do horário mensal*/
Route::get('/work-times', [UserController::class, 'manageWorkTimes'])->name('work-times.index');
Route::post('/work-times', [UserController::class, 'storeWorkTime'])->name('work-times.store');
