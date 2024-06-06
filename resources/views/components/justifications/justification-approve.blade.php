
<link rel="stylesheet" href="{{ asset('css/show-all.css') }}">

<div class="container">

    <section class="table__body">

        <table>
            <thead>
            <tr>
                <th> Nome </th>
                <th> Data de Falta </th>
                <th> Duração </th>
                <th> Estado </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $justification->absence->user->name }}</td>
                <td>{{ $justification->absence->absence_start_date . " - " . $justification->absence->absence_end_date }}</td>
                <td>{{ $duration }}</td>
                <td></td>
            </tr>
            </tbody>
        </table>

    </section>

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

    <a href="{{ url('/justification/'. $justification->id.'/download') }}" class="btn-detail-edit">Download</a>

    <a href="{{ url('/justification/'. $justification->id.'/approve') }}" class="btn-detail-edit">Aprovar</a>
    <a href="{{ url('/justification/'. $justification->id.'/reject') }}" class="btn-detail-edit">Rejeitar</a>

</div>
