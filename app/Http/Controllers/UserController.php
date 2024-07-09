<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Presence;
use App\Models\User;
use App\Models\Role;
use App\Models\User_Shift;
use App\Models\Vacation;
use App\Models\Work_Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAndExtendUserShifts();
        $users = User::orderBy('id', 'desc')->get();
        return view('pages.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAndExtendUserShifts();
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $users = User::all();
        return view('pages.users.create', ['users' => $users, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lógica de armazenamento anterior ou ajustada conforme necessário
    }

    /**
     * Display the specified resource.
     */

    public function show()
    {
        $this->checkAndExtendUserShifts();
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $user_shift = User_Shift::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->whereDate('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('pages.users.show', ['user' => $user, 'user_shift' => $user_shift]);
    }



    public function showSpec($id)
    {
        $this->checkAndExtendUserShifts();
        $user = User::find($id);
        $today = Carbon::today()->toDateString();
        $user_shifts = User_Shift::where('user_id', $user->id)->get();
        $user_shift = User_Shift::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->whereDate('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        return view('pages.users.show', ['user' => $user, 'user_shift' => $user_shift]);
    }




    public function showAll()
    {
        $this->checkAndExtendUserShifts();
        $today = Carbon::today()->toDateString();
        $users = User::all();
        foreach ($users as $user) {
            $user_shifts = User_Shift::where('user_id', $user->id)
                ->where(function ($query) use ($today) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $today);
                })
                ->whereDate('start_date', '<=', $today)
                ->orderBy('start_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            $user_shift = $user_shifts->sortByDesc('created_at')->first();
            $user->shift = $user_shift;
        }
        return view('pages.users.show-all', ['users' => $users]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $this->checkAndExtendUserShifts();
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $user = auth()->user();
        $today = Carbon::now();
        $user_shift = User_Shift::where('user_id', $user->id)
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('pages.users.edit', ['user' => $user, 'user_shift' => $user_shift, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }

    public function editSpec($id)
    {
        $this->checkAndExtendUserShifts();
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $user = User::find($id);
        $today = Carbon::now();
        $user_shift = User_Shift::where('user_id', $user->id)
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('pages.users.edit-spec', ['user' => $user, 'user_shift' => $user_shift, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->has('role')) {
            $role = Role::where('role', $request->input('role'))->first();
            if ($role) {
                $user->role_id = $role->id;
            }
        }
        $user->address = $request->input('address');
        $user->nif = $request->input('nif');
        $user->tel = $request->input('tel');
        $user->birth_date = $request->input('birth_date');
        $user->save();

        // Ao trocar de horário ele adiciona um user_shift
        $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();
        if ($user_shift) {
            $user_shift->end_date = now();
            $user_shift->save();
        }

        User_Shift::create([
            'user_id' => $user->id,
            'work_shift_id' => $request->input('work_shift_id'),
            'start_date' => now(),
            'end_date' => null,
        ]);

        return redirect('/user/show');
    }

    public function updateSpec(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('/user/show')->with('error', 'Usuário não encontrado!');
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->has('role')) {
            $role = Role::where('role', $request->input('role'))->first();
            if ($role) {
                $user->role_id = $role->id;
            }
        }
        $user->address = $request->input('address');
        $user->nif = $request->input('nif');
        $user->tel = $request->input('tel');
        $user->birth_date = $request->input('birth_date');
        $user->save();

        $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();
        if ($user_shift) {
            $user_shift->end_date = now();
            $user_shift->save();
        }

        User_Shift::create([
            'user_id' => $user->id,
            'work_shift_id' => $request->input('work_shift_id'),
            'start_date' => now(), // Salva a data e hora atuais
            'end_date' => null,
        ]);

        return redirect('/users/show-all')->with('success', 'Especificações do usuário atualizadas com sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $id = $user->id;
        $verAdmin = false;
        $ver = false;

        $users = User::all();

        // Verifica se o utilizador se está a apagar a si mesmo
        if($user->id == auth()->user()->id){
            return redirect('/users/show-all')->with('error', 'Não se pode apagar a si mesmo! Por favor, solicite a outro administrador.');
        }

        // Verifica se o utilizador é o único administrador
        if($user-> role_id == 3){
            $verAdmin = true;

            // Se houver outro admin
            foreach ($users as $userCicle) {
                if($userCicle-> role_id == 3 && $userCicle-> id != $id){
                    $user->delete();
                    return redirect('/users/show-all')->with('success', 'Usuário apagado com sucesso!');

                }
            }

            if($ver == false){
                return redirect('/users/show-all')->with('error', 'Não pode apagar o único administrador!');
            }

        }

        if($verAdmin == false){
            $user->delete();
            return redirect('/users/show-all')->with('success', 'Usuário apagado com sucesso!');
        }


    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $users = User::all();

        if (!$file) {
            return redirect()->back()->with('error', 'Escolha um ficheiro antes de importar.');
        }

        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'Erro ao abrir o ficheiro.');
        }

        $userData = []; // Array para armazenar os dados válidos

        // Ignorar a primeira linha (cabeçalhos)
        fgets($handle);

        // Faz as validações antes de inserir
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);
            if (count($data) != 9) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contem informações de utilizadores.');
            }

            // Verifica se os IDs são inteiros
            if (!is_numeric($data[0])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de permissão são números válidos.');
            }

            // Verifica se o email é válido
            if (!filter_var($data[6], FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Certifique-se que os emails são válidos.');
            }

            // Verifica se a data de nascimento é válida
            if (!strtotime($data[5])) {
                return redirect()->back()->with('error', 'Certifique-se que as datas de nascimento são válidas.');
            }

            // Verifica se o NIF é válido
            if (!is_numeric($data[3]) || strlen($data[3]) != 9) {
                return redirect()->back()->with('error', 'Certifique-se que os NIFs são válidos.');
            }

            // Verifica se o telefone é válido
            if (!is_numeric($data[4]) || strlen($data[3]) != 9) {
                return redirect()->back()->with('error', 'Certifique-se que os telefones são válidos.');
            }

            // Verifica se o role_id está entre 1 e 3
            if ($data[0] < 1 || $data[0] > 3) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de função estão entre 1 e 3.');
            }

            // Verifica se o work_shift_id existe
            $work_shift = Work_Shift::find($data[8]);

            if (!$work_shift) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de turno existem.');
            }

            // Armazenar os dados válidos no array
            $userData[] = [
                'role_id' => $data[0],
                'name' => $data[1],
                'address' => $data[2],
                'nif' => $data[3],
                'tel' => $data[4],
                'birth_date' => $data[5],
                'email' => $data[6],
                'password' => $data[7],
                'work_shift_id' => $data[8],
            ];
        }

        // Verifica se os emails são únicos
        $numeroUsers = count($userData);
        $numeroUsersAtuais = count($users);

        for ($i = 0; $i < $numeroUsers; $i++) {
            for ($j = 0; $j < $numeroUsers; $j++) {
                if ($userData[$i]['email'] == $userData[$j]['email'] && $i != $j) {
                    return redirect()->back()->with('error', 'Não pode haver utilizadores com o mesmo email.');
                }
            }
        }

        // Verifica se existem users com o mesmo nif
        for ($i = 0; $i < $numeroUsers; $i++) {
            for ($j = 0; $j < $numeroUsers; $j++) {
                if ($userData[$i]['nif'] == $userData[$j]['nif'] && $i != $j) {
                    return redirect()->back()->with('error', 'Não pode haver utilizadores com o mesmo nif.');
                }
            }
        }
        fclose($handle);

        // Inserir os dados válidos na base de dados
        foreach ($userData as $data) {
            $ver = false;

            for ($i = 0; $i < $numeroUsersAtuais; $i++) {
                if ($data['nif'] == $users[$i]['nif']) {
                    $ver = true;
                }
            }

            if ($ver == false) {
                $user = User::create([
                    'role_id' => $data['role_id'],
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'nif' => $data['nif'],
                    'tel' => $data['tel'],
                    'birth_date' => $data['birth_date'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                ]);

                // Criação do turno do usuário
                $user_shift = new User_Shift();
                $user_shift->work_shift_id = $data['work_shift_id'];
                $user_shift->user_id = $user->id;
                $user_shift->start_date = now();
                $user_shift->end_date = null;
                $user_shift->save();
            }
        }

        return redirect()->back()->with('success', 'Utilizadores importados com sucesso.');
    }

    public function export()
    {
        $work_shifts = Work_Shift::all();
        $users = User::all();
        $csvFileName = 'users.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');

        // Coloca o header no ficheiro
        fputcsv($handle, ['Role_id', 'Nome', 'Rua', 'Nif', 'Telemovel', 'Data_Nascimento', 'Email', 'Password', 'User_Work_Shift_Id']);

        // Imprime cada utilizador no ficheiro csv
        foreach ($users as $user) {
            $user_shift = User_Shift::where('user_id', $user->id)->orderBy('id', 'desc')->first();

            fputcsv($handle, [$user->role_id, $user->name, $user->address, $user->nif, $user->tel, $user->birth_date, $user->email, $user->password, $user_shift->work_shift_id]); // Add more fields as needed
        }

        fclose($handle);

        return Response::make('', 200, $headers);
    }

    // Adicionar os métodos de WorkTimeController aqui
    public function manageWorkTimes()
    {
        $this->checkAndExtendUserShifts();
        // Obtém todos os usuários com seus turnos de trabalho
        $users = User::with('user_shifts.work_shift')->get();
        $workShifts = Work_Shift::all();
        return view('pages.work-times.index', compact('users', 'workShifts'));
    }

    public function storeWorkTime(Request $request)
    {
        $this->checkAndExtendUserShifts();
        // Valida os dados enviados pelo formulário
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_shift_id' => 'required|exists:work_shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Fechar o horário de trabalho anterior
        $previousShift = User_Shift::where('user_id', $validated['user_id'])
            ->whereNull('end_date')
            ->orWhere('end_date', '>', $validated['start_date'])
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($previousShift) {
            $previousShift->end_date = Carbon::parse($validated['start_date'])->subSecond()->format('Y-m-d 23:59:59');
            $previousShift->save();
        }

        // Adiciona hora padrão ao start_date e end_date
        $startDateTime = Carbon::parse($validated['start_date'])->startOfDay();
        $endDateTime = isset($validated['end_date']) ? Carbon::parse($validated['end_date'])->endOfDay() : null;

        // Cria um novo turno de trabalho para o usuário
        $newShift = User_Shift::create([
            'user_id' => $validated['user_id'],
            'work_shift_id' => $validated['work_shift_id'],
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
        ]);

        return redirect()->route('work-times.index')->with('success', 'Work time added successfully.');
    }




    private function checkAndExtendUserShifts()
    {
        $today = Carbon::today();
        $userShifts = User_Shift::where('end_date', '<', $today)->get();

        foreach ($userShifts as $userShift) {
            if (Carbon::parse($userShift->end_date)->lt($today)) {
                $nextDay = Carbon::parse($userShift->end_date)->addDay();

                $existingShift = User_Shift::where('user_id', $userShift->user_id)
                    ->where('start_date', '=', $nextDay->toDateString())
                    ->first();

                if (!$existingShift) {
                    User_Shift::create([
                        'user_id' => $userShift->user_id,
                        'work_shift_id' => $userShift->work_shift_id,
                        'start_date' => $nextDay->toDateString() . ' 00:00:00',
                        'end_date' => null,
                    ]);
                }
            }
        }

        // Fechar qualquer turno com end_date nulo que tenha um próximo turno
        $openShifts = User_Shift::whereNull('end_date')->get();

        foreach ($openShifts as $openShift) {
            $nextShift = User_Shift::where('user_id', $openShift->user_id)
                ->where('start_date', '>', $openShift->start_date)
                ->orderBy('start_date', 'asc')
                ->first();

            if ($nextShift) {
                $openShift->end_date = Carbon::parse($nextShift->start_date)->subDay()->endOfDay()->toDateTimeString();
                $openShift->save();
            }
        }
    }

}
?>
