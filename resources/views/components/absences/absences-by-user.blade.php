<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">
<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">


<main class="table" id="users_table">

    <section class="table__header">
        <h1>Lista de faltas</h1>
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

        <table>
            <thead>
            <tr>
                <th class="id_cell"> Id <span class="icon-arrow">&UpArrow;</span></th>
                <th> Data Inicio <span class="icon-arrow">&UpArrow;</span></th>
                <th> Data Fim <span class="icon-arrow">&UpArrow;</span></th>
                <th> Tipo de Falta <span class="icon-arrow">&UpArrow;</span></th>
                <th> Estado <span class="icon-arrow">&UpArrow;</span></th>
                <th> Justificar <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($absences as $absence)
                <tr>
                    <td>{{ $absence->id }}</td>
                    <td>{{ $absence->absence_start_date }}</td>
                    <td>{{ $absence->absence_end_date}}</td>
                    <td>
                        @foreach($absences_states as $absences_state)
                            @if($absence->absence_states_id == $absences_state->id)
                                {{ $absences_state->description}}
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($absences_types as $absences_type)
                            @if($absence->absence_types_id == $absences_type->id)
                                {{ $absences_type->description}}
                            @endif
                        @endforeach
                    </td>
                    <td>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
</main>

<script src="{{ asset('js/show-all.js') }}"></script>


