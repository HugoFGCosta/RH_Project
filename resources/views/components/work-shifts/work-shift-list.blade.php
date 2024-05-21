<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">

<a href="{{ url('work-shifts/create') }}" type="button"><button class="sub-menu">Criar Turno</button></a>

<table>
    <thead>
    <tr>
        <th scope="col">#</th>
        <th class="ola" scope="col">Horario de Inicio</th>
        <th scope="col">Hora de Fim</th>
        <th scope="col">Horario de almoço</th>
        <th scope="col">Dias da semana</th>
        <th scope="col">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($workShifts as $workShift)
        <tr>
            <td>{{ $workShift->id }}</td>
            <td>{{ $workShift->start_hour }}</td>
            <td>{{ $workShift->end_hour }}</td>
            <td>{{ $workShift->break_start." - ".$workShift->break_end }}</td>
            <td>Segunda|Terça|Quarta|Quinta|Sexta</td>

            <td>
                @auth
                    <div class="buttonDiv">
                        <div>
                            <a href="{{ url('work-shifts/'.$workShift->id) }}" type="button"><button class="sub-menu">Mostrar Dados</button></a>
                        </div>
                        <form action="{{ url('work-shifts/'.$workShift->id) }}" method='POST'>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn deleteButton">Delete</button>
                        </form>
                    </div>
                @endauth
            </td>
        </tr>
    @endforeach
    </tbody>

</table>



