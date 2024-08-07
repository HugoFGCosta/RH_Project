<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">
<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">

<main class="table custom-height" id="users_table">

    <section class="table__header">
        <div class="button-create">
            <a href="{{ url('work-shifts/create') }}" type="button">Criar Turno</a>
        </div>
        <div class="input-group">
            <input type="search" placeholder="Procurar...">
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
        <table>
            <thead>
            <tr>
                <th class="id_cell"> Id <span class="icon-arrow">&UpArrow;</span></th>
                <th> Inicio <span class="icon-arrow">&UpArrow;</span></th>
                <th> Fim <span class="icon-arrow">&UpArrow;</span></th>
                <th> Intervalo <span class="icon-arrow">&UpArrow;</span></th>
                <th> Dias da Semana <span class="icon-arrow">&UpArrow;</span></th>
                <th> Detalhe/Editar <span class="icon-arrow">&UpArrow;</span></th>
                <th class="delete_cell"> Delete <span class="icon-arrow">&UpArrow;</span></th>
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
                        <form action="{{ url('work-shifts', $workShift->id) }}" method="POST" style="display:inline;"
                              class="no-form">
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
