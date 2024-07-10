@php
    use Carbon\Carbon;
@endphp

<main class="table" id="vacations_table">
    <section class="table__header">
        <a href="vacations/create"><button class="new__vacation">Marcar Férias</button></a>
        <h1>{{ 22 - $totaldias }} dias de férias por marcar</h1>
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
                <th> Id <span class="icon-arrow">&UpArrow;</span></th>
                <th> Nome <span class="icon-arrow">&UpArrow;</span></th>
                <th> Estado <span class="icon-arrow">&UpArrow;</span></th>
                <th> Processado por <span class="icon-arrow">&UpArrow;</span></th>
                <th> De <span class="icon-arrow">&UpArrow;</span></th>
                <th> Até <span class="icon-arrow">&UpArrow;</span></th>
                <th> Editar <span class="icon-arrow">&UpArrow;</span></th>
                <th> Apagar <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach($vacations as $vacation)
                <tr>
                    <td>{{ $vacation->id }}</td>
                    <td>{{ $vacation->user->name }}</td>
                    <td>
                        @if($vacation->vacation_approval_states_id == 3)
                            <ion-icon name="hourglass-outline" size="large"></ion-icon>
                        @elseif($vacation->vacation_approval_states_id == 2)
                            <ion-icon class="red-cross" name="close-circle-outline" size="large"></ion-icon>
                        @elseif($vacation->vacation_approval_states_id == 1)
                            <ion-icon class="green-check" name="checkmark-circle-outline" size="large"></ion-icon>
                        @else
                            <img src="" height="33px" width="63px" alt="">
                        @endif
                    </td>
                    <td>{{ $vacation->approvedBy ? $vacation->approvedBy->name : '' }}</td>
                    <td>{{ $vacation->date_start }}</td>
                    <td>{{ $vacation->date_end }}</td>
                    <td>
                        @auth
                            <a href="{{ url('vacations/edit/' . $vacation->id) }}" type="button" class="btn-detail-edit">Editar</a>
                        @endauth
                    </td>
                    <td>
                        @auth
                            <form action="{{ url('vacations/delete/' . $vacation->id) }}" method="POST" style="display:inline;" class="no-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button">Apagar</button>
                            </form>
                        @endauth
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
</main>
