<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">
<link rel="stylesheet" href="{{ asset('css/absences-list.css') }}">


<main class="table" id="users_table">

    <section class="table__header">
        <h1>Lista de faltas (Gestão)</h1>
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
                <th> Id Falta<span class="icon-arrow">&UpArrow;</span></th>
                <th> Nome <span class="icon-arrow">&UpArrow;</span></th>
                <th> Data Falta<span class="icon-arrow">&UpArrow;</span></th>
                <th> Motivo <span class="icon-arrow">&UpArrow;</span></th>
                <th> Duração <span class="icon-arrow">&UpArrow;</span></th>
                <th> Estado <span class="icon-arrow">&UpArrow;</span></th>
                <th> Data Justificação <span class="icon-arrow">&UpArrow;</span></th>
                <th> Observações<span class="icon-arrow">&UpArrow;</span></th>
                <th> Justificar <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($absences as $absence)
                <tr>
                    <td class="idCell">{{$absence->id}}</td>
                    <td class="nameCell">{{$absence->user->name}}</td>
                    <td class="dateAbsenceCell">{{$absence->absences_start_date." ". $absence->absence_end_date}}</td>
                    <td class="motiveCell">
                        @if ($absence->justification)
                            {{ $absence->justification->motive }}
                        @else
                            -----
                        @endif
                    </td>
                    <td class="durationCell">

                        @php

                            $datetime1 = new \DateTime($absence->absence_start_date);
                            $datetime2 = new \DateTime($absence->absence_end_date);
                            $interval = $datetime1->diff($datetime2);
                            $duration = $interval->format('%H:%I');

                        @endphp

                        {{$duration . " H:M"}}
                    </td>
                    <td class="stateCell">
                        @foreach($absences_states as $absences_state)
                            @if($absence->absence_states_id == $absences_state->id)
                                @if($absences_state->description == "Injustificado")
                                    Por Justificar
                                @else
                                    {{ $absences_state->description}}
                                @endif
                            @endif
                        @endforeach
                    </td>
                    <td class="dateJustificationCell">
                        @if ($absence->justification)
                            {{ $absence->justification->justification_date }}
                        @else
                            -----
                        @endif
                    </td>
                    <td class="observationCell">
                        @if ($absence->justification)
                            {{ $absence->justification->observation }}
                        @else
                            -----
                        @endif
                    </td>
                    <td class="buttonCell">
                        @foreach($absences_states as $absences_state)
                            @if($absence->absence_states_id == $absences_state->id)
                                @if($absences_state->description == "Pendente")
                                    <a href="{{ url('/justification/'. $absence->justification->id.'/manage') }}" class="btn-detail-edit">Aprovar/Rejeitar</a>
                                @elseif ($absences_state->description == "Aprovado")
                                    Aprovado
                                @elseif ($absences_state->description == "Rejeitado")
                                    Rejeitado
                                @else
                                    <a href="{{ url('absences/' . $absence->id . '/justification/create') }}" class="btn-detail-edit">Justificar</a>
                                @endif
                            @endif
                        @endforeach
                    </td>

                </tr>

            @endforeach
            </tbody>
        </table>
    </section>
</main>

<script src="{{ asset('js/show-all.js') }}"></script>
<script src="{{asset('js/absences-list.js')}}"></script>


