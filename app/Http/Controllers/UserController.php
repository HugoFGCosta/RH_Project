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
use Illuminate\Validation\Rule;
use Validator;

class UserController extends Controller
{
    // Metodo Index - Lista todos os users.    NAO ESTA SENDO UTILIZADO
    public function index()
    {

        $this->checkAndExtendUserShifts();
        $users = User::orderBy('id', 'desc')->get();
        return view('pages.users.index', ['users' => $users]);
    }


    // Metodo Create - Cria um user com cargo, turno
    public function create()
    {
        $this->checkAndExtendUserShifts();
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $users = User::all();
        return view('pages.users.create', ['users' => $users, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }


    public function store(Request $request)
    {
        // Lógica de armazenamento anterior ou ajustada conforme necessário
    }


    // Metodo Show - Mostra todas as informaçoes do user logado.
    public function show()
    {
        $this->checkAndExtendUserShifts();
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // Atribui ao $user_shift o qual turno está através da query comparando com o dia atual obtido na variavel $today
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


    // Metodo showSpec - Mostra um user especifico da lista showAll com todas as informaçoes recebendo um $id por parametro.
    public function showSpec($id)
    {

        $this->checkAndExtendUserShifts();
        $user = User::find($id);
        $today = Carbon::today()->toDateString();

        // Atribui ao $user_shift o qual turno está através da query comparando com o dia atual obtido na variavel $today
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


    // Metodo showAll - Lista todos os users.
    public function showAll()
    {

        $this->checkAndExtendUserShifts();
        $today = Carbon::today()->toDateString();
        $users = User::all();

        // Atribui a todos os users um turno através da query comparando com o dia atual obtido na variavel $today
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


    // Metodo edit - Edita as informaçoes do user que está logado.
    public function edit()
    {

        $this->checkAndExtendUserShifts();
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $user = auth()->user();
        $today = Carbon::now();
        $user_shift = User_Shift::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('pages.users.edit', ['user' => $user, 'user_shift' => $user_shift, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }


    // Metodo editSpec - Edita um user especifico do metodo showAll recebendo o $id como parametro.
    public function editSpec($id)
    {

        $this->checkAndExtendUserShifts();
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $user = User::find($id);
        $today = Carbon::now();
        $user_shift = User_Shift::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('pages.users.edit-spec', ['user' => $user, 'user_shift' => $user_shift, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }


    // Metodo update - Responsavel por verificar as novas informaçoes e validar para que atualize os dados.
    public function update(Request $request)
    {
        $messages = [
            'name.required' => 'O nome é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'address.required' => 'O endereço é obrigatório.',
            'nif.required' => 'O Número de Identificação Fiscal (NIF) é obrigatório.',
            'nif.digits' => 'O Número de Identificação Fiscal (NIF) deve conter exatamente 9 dígitos.',
            'tel.required' => 'O número de telemóvel é obrigatório.',
            'tel.digits' => 'O número de telemóvel deve conter exatamente 9 dígitos.',
            'tel.unique' => 'Este telemóvel já se encontra registado.',
            'birth_date.required' => 'A data de aniversário é obrigatório.',
            'birth_date.before' => 'É necessário ter 18 anos ou mais.',
            'work_shift_id.required' => 'O turno é obrigatório.',
        ];

        $user = auth()->user();

        Validator::make($request->all(), [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'address' => 'required',
            'nif' => 'required|digits:9',
            'tel' => [
                'required',
                'digits:9',
                Rule::unique('users', 'tel')->ignore($user->id),
            ],
            'birth_date' => ['required', 'date', 'before:-18 years'],
            'work_shift_id' => 'required',
        ], $messages)->validate();

        // Verifica se o e-mail já está sendo usado por outro usuário
        $existingUser = User::where('email', $request->email)->where('id', '<>', $user->id)->first();
        if ($existingUser) {
            return redirect()->back()->withErrors(['email' => 'Este e-mail já está em uso.']);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('role')) {
            $role = Role::where('role', $request->input('role'))->first();
            if ($role) {
                $user->role_id = $role->id;
            }
        }
        $user->address = $request->address;
        $user->nif = $request->nif;
        $user->tel = $request->tel;
        $user->birth_date = $request->birth_date;
        $user->save();

        // Ao trocar de horário ele adiciona um user_shift
        $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();
        if ($user_shift) {
            $user_shift->end_date = now();
            $user_shift->save();
        }

        User_Shift::create([
            'user_id' => $user->id,
            'work_shift_id' => $request->work_shift_id,
            'start_date' => now(),
            'end_date' => null,
        ]);

        return redirect('/user/show');
    }



    // Metodo updateSpec - Responsavel por verificar as novas informaçoes e validar para que atualize os dados para um user especifico.

    public function updateSpec(Request $request, $id)
    {
        $messages = [
            'name.required' => 'O nome é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'role_id.required' => 'O cargo é obrigatório.',
            'address.required' => 'O endereço é obrigatório.',
            'nif.required' => 'O Número de Identificação Fiscal (NIF) é obrigatório.',
            'nif.digits' => 'O Número de Identificação Fiscal (NIF) deve conter exatamente 9 dígitos.',
            'tel.required' => 'O número de telemóvel é obrigatório.',
            'tel.digits' => 'O número de telemóvel deve conter exatamente 9 dígitos.',
            'tel.unique' => 'Este telemóvel já se encontra registado.',
            'birth_date.required' => 'A data de aniversário é obrigatória.',
            'birth_date.before' => 'É necessário ter 18 anos ou mais.',
            'work_shift_id.required' => 'O turno é obrigatório.',
        ];

        Validator::make($request->all(), [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'address' => 'required',
            'nif' => 'required|digits:9',
            'tel' => [
                'required',
                'digits:9',
                Rule::unique('users', 'tel')->ignore($id),
            ],
            'birth_date' => ['required', 'date', 'before:-18 years'],
            'work_shift_id' => 'required',
        ], $messages)->validate();

        $user = User::find($id);
        if (!$user) {
            return redirect('/user/show')->with('error', 'Utilizador não encontrado!');
        }

        // Verifica se o e-mail já está sendo usado por outro usuário
        $existingUser = User::where('email', $request->email)->where('id', '<>', $user->id)->first();
        if ($existingUser) {
            return redirect()->back()->withErrors(['email' => 'Este e-mail já está em uso.']);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('role')) {
            $role = Role::where('role', $request->input('role'))->first();
            if ($role) {
                $user->role_id = $role->id;
            }
        }
        $user->address = $request->address;
        $user->nif = $request->nif;
        $user->tel = $request->tel;
        $user->birth_date = $request->birth_date;
        $user->save();

        // Atualiza o turno do usuário
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

        return redirect('/users/show-all')->with('success', 'Especificações do utilizador atualizadas com sucesso!');
    }


    // Metodo destroy - Recebe por parametro o $id para que apague um user.
    public function destroy($id)
    {
        // Validação - Caso haja apenas 1 admin nao é possivel apaga-lo.

        $user = User::findOrFail($id);
        $id = $user->id;
        $verAdmin = false;
        $ver = false;

        $users = User::all();

        // Verifica se o utilizador se está a apagar a si mesmo
        if ($user->id == auth()->user()->id) {
            return redirect('/users/show-all')->with('error', 'Não se pode apagar a si mesmo! Por favor, solicite a outro administrador.');
        }

        // Verifica se o utilizador é o único administrador
        if ($user->role_id == 3) {
            $verAdmin = true;

            // Se houver outro admin
            foreach ($users as $userCicle) {
                if ($userCicle->role_id == 3 && $userCicle->id != $id) {
                    $user->delete();
                    return redirect('/users/show-all')->with('success', 'Utilizador apagado com sucesso!');

                }
            }

            if ($ver == false) {
                return redirect('/users/show-all')->with('error', 'Não pode apagar o único administrador!');
            }

        }

        if ($verAdmin == false) {
            $user->delete();
            return redirect('/users/show-all')->with('success', 'Utilizador apagado com sucesso!');
        }


    }

    // Metodo Import - Serve para importar utilizadores para a base de dados
    public function import(Request $request)
    {

        // Vai buscar o ficheiro inserido no formulário
        $file = $request->file('file');
        $users = User::all();

        // Se o ficheiro não foi submetido mostra mensagem de erro
        if (!$file) {
            return redirect()->back()->with('error', 'Escolha um ficheiro antes de importar.');
        }

        $handle = fopen($file->getPathname(), 'r');

        // Se por algum motivo houver erro ao abrir o ficheiro mostra mensagem de erro
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

            // Verifica se o work_shift_id existe
            $work_shift = Work_Shift::find($data[8]);

            if (!$work_shift) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de turno existem.');
            }

            // Verifica se o role_id existe
            $role = Role::find($data[0]);

            if (!$role) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de role existem (entre 1 e 3).');
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

        // Percorre os emails e verifica se existe algum email repetido
        for ($i = 0; $i < $numeroUsers; $i++) {
            for ($j = 0; $j < $numeroUsers; $j++) {
                if ($userData[$i]['email'] == $userData[$j]['email'] && $i != $j) {
                    return redirect()->back()->with('error', 'Não pode haver utilizadores com o mesmo email.');
                }
            }
        }

        // Percorre os emails e verifica se existe algum nif repetido
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

                // Criação do turno do utilizador
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


    // Metodo Export- Serve para exportar as informaçóes relativamente aos utilizadores existentes
    public function export()
    {
        $work_shifts = Work_Shift::all();
        $users = User::all();
        $csvFileName = 'users.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        // Cria um buffer para armazenar o conteúdo CSV temporariamente
        $output = fopen('php://temp', 'r+');

        // Coloca o header no ficheiro
        fputcsv($output, ['Role_id', 'Nome', 'Rua', 'Nif', 'Telemovel', 'Data_Nascimento', 'Email', 'Password', 'User_Work_Shift_Id']);

        // Imprime cada utilizador no ficheiro csv
        foreach ($users as $user) {
            $user_shift = User_Shift::where('user_id', $user->id)->orderBy('id', 'desc')->first();

            fputcsv($output, [$user->role_id, $user->name, $user->address, $user->nif, $user->tel, $user->birth_date, $user->email, $user->password, $user_shift->work_shift_id]); // Adicione mais campos conforme necessário
        }

        // Volta para o início do buffer para leitura
        rewind($output);

        // Captura o conteúdo CSV
        $csvContent = stream_get_contents($output);

        // Fecha o buffer
        fclose($output);

        // Retorna a resposta com o conteúdo CSV e os headers apropriados
        return response($csvContent, 200, $headers);
    }

    public function manageWorkTimes()
    {
        $this->checkAndExtendUserShifts();
        // Obtém todos os utilizadores com seus turnos de trabalho
        $users = User::with('user_shifts.work_shift')->get();
        $workShifts = Work_Shift::all();
        return view('pages.work-times.index', compact('users', 'workShifts'));
    }

    public function storeWorkTime(Request $request)
    {
        $this->checkAndExtendUserShifts();

        // Manual validation
        $user_id = $request->input('user_id');
        $work_shift_id = $request->input('work_shift_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (!$user_id || !User::find($user_id)) {
            return redirect()->back()->with('error', 'O user_id deve existir na tabela de utilizadores.');
        }

        if (!$work_shift_id || !Work_Shift::find($work_shift_id)) {
            return redirect()->back()->with('error', 'Work_shift_id é obrigatório deve existir na tabela de turnos de trabalho.');
        }

        if (!$start_date || !strtotime($start_date)) {
            return redirect()->back()->with('error', 'A data de início é obrigatória e deve ser uma data válida.');
        }

        if ($end_date && strtotime($end_date) === false) {
            return redirect()->back()->with('error', 'A data de fim é obrigatória e deve ser uma data válida.');
        }

        if ($end_date && strtotime($start_date) > strtotime($end_date)) {
            return redirect()->back()->with('error', 'A data de início não pode ser após que a data de fim.');
        }

        // Fechar o horário de trabalho anterior
        $previousShift = User_Shift::where('user_id', $user_id)
            ->whereNull('end_date')
            ->orWhere('end_date', '>', $start_date)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($previousShift) {
            $previousShift->end_date = Carbon::parse($start_date)->subSecond()->format('Y-m-d 23:59:59');
            $previousShift->save();
        }

        // Adiciona hora padrão ao start_date e end_date
        $startDateTime = Carbon::parse($start_date)->startOfDay();
        $endDateTime = $end_date ? Carbon::parse($end_date)->endOfDay() : null;

        // Cria um novo turno de trabalho para o utilizador
        $newShift = User_Shift::create([
            'user_id' => $user_id,
            'work_shift_id' => $work_shift_id,
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
        ]);

        return redirect()->route('work-times.index')->with('success', 'Turno adicionado com sucesso.');
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