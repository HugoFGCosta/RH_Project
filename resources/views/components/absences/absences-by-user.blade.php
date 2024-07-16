<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">
<link rel="stylesheet" href="{{ asset('css/absences-by-user.css') }}">

<p class="messageError" id="messageError"></p>

<form id="abcenseForm" action="/justification/create">

    <main class="table" id="users_table">
        <section class="table__header">
            <input class="submitButton" type="submit" value="Justificar">
            <div class="input-group">
                <input type="search" placeholder="Pesquisar...">
                <ion-icon name="search-outline"></ion-icon>
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Exportar como &nbsp; &#10140;</label>
                    <label for="export-file" id="toPDF">PDF</label>
                    <label for="export-file" id="toJSON">JSON</label>
                    <label for="export-file" id="toCSV">CSV</label>
                    <label for="export-file" id="toEXCEL">EXCEL</label>
                </div>
            </div>
        </section>
        <section class="table__body">
            <table>
                <thead>
                <tr>
                    <th class="id_cell">Id <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Data Inicio <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Data Fim <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Tipo de Falta <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Estado <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Justificar <span class="icon-arrow">&UpArrow;</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($absences as $absence)
                    <tr>
                        <td class="idCell">{{ $absence->id }}</td>
                        <td class="absenceStartCell">{{ $absence->absence_start_date }}</td>
                        <td class="absenceEndCell">{{ $absence->absence_end_date }}</td>
                        <td class="absenceTypeCell">
                            @if($absence->absence_types_id == 1)
                                Primeiro Turno
                            @elseif($absence->absence_types_id == 2)
                                Segundo Turno
                            @else
                                Total
                            @endif
                        </td>
                        <td class="absenceStateCell">
                            @foreach($absences_states as $absences_state)
                                @if($absence->absence_states_id == $absences_state->id)
                                    @if($absences_state->description == "Injustificado")
                                        Por Justificar
                                    @else
                                        {{ $absences_state->description }}
                                    @endif
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @if($absence->absence_states_id != 4)
                                <input class="checkBoxAbsence" type="checkbox" name="selected_absences[]" value="{{ $absence->id }}" disabled>
                            @else
                                <input class="checkBoxAbsence" type="checkbox" name="selected_absences[]" value="{{ $absence->id }}">
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    </main>
</form>

