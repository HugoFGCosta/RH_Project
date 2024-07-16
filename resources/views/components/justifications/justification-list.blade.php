@php
    use Carbon\Carbon;
    // Generate arrays for months and years
    $months = [
        'Janeiro' => '01', 'Fevereiro' => '02', 'Março' => '03', 'Abril' => '04',
        'Maio' => '05', 'Junho' => '06', 'Julho' => '07', 'Agosto' => '08',
        'Setembro' => '09', 'Outubro' => '10', 'Novembro' => '11', 'Dezembro' => '12'
    ];
    $years = range(Carbon::now()->year, Carbon::now()->year - 10);
@endphp
<link rel="stylesheet" href="{{ asset('css/work-shifts.css') }}">
<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">
<link rel="stylesheet" href="{{ asset('css/justification-list.css') }}">
<main class="table" id="users_table">
    <section class="table__header input-group-wrapper">
        <div class="input-group">
            <input type="search" placeholder="Procurar...">
            <ion-icon name="search-outline"></ion-icon>
        </div>
        <!-- Month Filter -->
        <div class="input-group-filter no-export">
            <select id="monthFilter">
                <option value="">Selecionar Mês</option>
                @foreach(['Janeiro' => '01', 'Fevereiro' => '02', 'Março' => '03', 'Abril' => '04', 'Maio' => '05', 'Junho' => '06', 'Julho' => '07', 'Agosto' => '08', 'Setembro' => '09', 'Outubro' => '10', 'Novembro' => '11', 'Dezembro' => '12'] as $month => $value)
                    <option value="{{ $month }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>
        <!-- Year Filter -->
        <div class="input-group-filter no-export">
            <select id="yearFilter">
                <option value="">Selecionar Ano</option>
                @foreach(range(date('Y'), date('Y') - 10) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
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
                                <a href="{{ url('/justification/'. $justification->id.'/manage') }}"
                                   class="btn-detail-edit">Aprovar/Rejeitar</a>
                                @break;
                            @elseif($absence->absence_states_id == 1)
                                Aprovado
                                @break;
                                «
                            @elseif($absence->absence_states_id == 2)
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

