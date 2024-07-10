<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">
<link rel="stylesheet" href="{{ asset('css/justification-approve.css') }}">

<div class="container justificationContainer">

<h3 class="tableTitle">Falta</h3>

    <section class="table__body">

        <table class="absenceTable">
            <thead>
            <tr>
                <th> Nome </th>
                <th> Data de Falta </th>
                <th> Duração </th>
            </tr>
            </thead>
            <tbody>
            @foreach($justification->absences as $index => $absence)
                <tr>
                    <td>{{ $absence->user->name }}</td>
                    <td>{{ $absence->absence_start_date . " - " . $absence->absence_end_date }}</td>
                    <td>{{ $durations[$index] . " hora(as)" }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </section>

    <h3 class="tableTitle">Justificação</h3>

    <section class="table__body">

        <table>
            <thead>
            <tr>
                <th> Motivo </th>
                <th> Data de Justificação </th>
                <th> Observações </th>
                <th> Ficheiro </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $justification->motive}}</td>
                <td>{{ $justification->justification_date}}</td>
                <td>{{ $justification->observation}}</td>
                <td>{{ $justification->file}}</td>

            </tr>
            </tbody>
        </table>

    </section>

    <div class="buttonsDiv">
    <a href="{{ url('/justification/'. $justification->id.'/download') }}" class="btn-detail-edit buttonJustification">Download</a>
    <a href="{{ url('/justification/'. $justification->id.'/approve') }}" class="btn-detail-edit buttonJustification">Aprovar</a>
    <a href="{{ url('/justification/'. $justification->id.'/reject') }}" class="btn-detail-edit buttonJustification">Rejeitar</a>
    </div>


</div>
