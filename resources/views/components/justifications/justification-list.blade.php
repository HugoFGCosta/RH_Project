<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">
<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">
<link rel="stylesheet" href="{{ asset('css/justification-list.css') }}">


<main class="table" id="users_table">

    <section class="table__header">
        <h1>Lista de justificações</h1>
        <div class="input-group">
            <input type="search" placeholder="Procurar...">
            <ion-icon name="search-outline"></ion-icon>
        </div>
        <div class="export__file">
            <label for="export-file" class="export__file-btn" title="Export File"></label>
            <input type="checkbox" id="export-file">
            <div class="export__file-options">
                <label>Exportar como &nbsp; &#10140;</label>
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
                <th> Motivo <span class="icon-arrow">&UpArrow;</span></th>
                <th> Data da Justificação <span class="icon-arrow">&UpArrow;</span></th>
                <th> Observações <span class="icon-arrow">&UpArrow;</span></th>
                <th> Estado <span class="icon-arrow">&UpArrow;</span></th>
                <th class="rightCell"> Ações <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($justifications as $justification)
                <tr>
                    <td class="motiveCell">{{ $justification->motive }}</td>
                    <td class="justificationDateCell">{{ $justification->justification_date }}</td>
                    <td class="observationCell">{{ $justification->observation }}</td>
                    <td class="stateCell">
                        @foreach($justification->absences as $absence)
                            @if($absence->absence_states_id == 1)
                                Aprovado
                                @break
                            @elseif($absence->absence_states_id == 2)
                                Rejeitado
                                @break
                            @elseif($absence->absence_states_id == 3)
                                Aguarda validação
                                @break
                            @else
                                Injustificado
                                @break
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($justification->absences as $absence)
                            @if($absence->absence_states_id == 3)
                                <a href="{{ url('/justification/'. $justification->id.'/manage') }}" class="btn-detail-edit">Aprovar/Rejeitar</a>
                                @break;
                            @elseif($absence->absence_states_id == 1)
                                Aprovado
                                @break;
«                           @elseif($absence->absence_states_id == 2)
                                Rejeitado
                                @break;
                            @elseif($absence->absence_states_id == 4)
                                Injustificado
                                @break;
                            @else
                                Injustificado Permanentemente
                                @break;
                            @endif

                        @endforeach

                    </td>


                </tr>
            @endforeach
            </tbody>
        </table>

    </section>
</main>



