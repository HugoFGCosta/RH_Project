<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">
<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">


<main class="table" id="users_table">

    <section class="table__header">
        <h1>Lista de horários</h1>
        <div class="input-group">
            <input type="search" placeholder="Search Data...">
            <ion-icon name="search-outline"></ion-icon>
        </div>
        <div class="export__file">
            <label for="export-file" class="export__file-btn" title="Export File"></label>
            <input type="checkbox" id="export-file">
            <div class="export__file-options">
                <label>Export As &nbsp; &#10140;</label>
                <label for="export-file" id="toPDF">PDF</label>
                <label for="export-file" id="toJSON">JSON</label>
                <label for="export-file" id="toCSV">CSV </label>
                <label for="export-file" id="toEXCEL">EXCEL</label>
            </div>
        </div>
    </section>
    <section class="table__body">
        <a href="{{ url('work-shifts/create') }}" type="button"><button class="sub-menu indexCreateButton">Criar Turno</button></a>

        <table>
            <thead>
            <tr>
                <th> Id <span class="icon-arrow">&UpArrow;</span></th>
                <th> Horario de Inicio <span class="icon-arrow">&UpArrow;</span></th>
                <th> Horario de Fim <span class="icon-arrow">&UpArrow;</span></th>
                <th> Horario de Almoço <span class="icon-arrow">&UpArrow;</span></th>
                <th> Dias da Semana <span class="icon-arrow">&UpArrow;</span></th>
                <th> Detalhe/Editar <span class="icon-arrow">&UpArrow;</span></th>
                <th> Delete <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($workShifts as $workShift)
                <tr>
                    <td>{{ $workShift->id }}</td>
                    <td>{{ $workShift->start_hour }}</td>
                    <td>{{ $workShift->end_hour}}</td>
                    <td>{{ $workShift->break_start." - ".$workShift->break_end }}</td>
                    <td>Segunda|Terça|Quarta|Quinta|Sexta</td>

                    <td>
                        <a href="{{ url('work-shifts', $workShift->id) }}" class="btn-detail-edit">Detalhe/Editar</a>
                    </td>
                    <td>
                        <form action="{{ url('work-shifts', $workShift->id) }}" method="POST" style="display:inline;" class="no-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">Apagar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
</main>

<script src="{{ asset('js/show-all.js') }}"></script>


