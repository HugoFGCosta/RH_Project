<main class="table" id="users_table">
    <section class="table__header">
        <h1>Registo de Assiduidade</h1>
        <div class="input-group">
            <input type="search" placeholder="Pesquisar...">
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
                <th>Data<span class="icon-arrow">&UpArrow;</span></th>
                <th class="limit">Escala Horário<span class="icon-arrow">&UpArrow;</span></th>
                <th>Entrada<span class="icon-arrow">&UpArrow;</span></th>
                <th>Saída<span class="icon-arrow">&UpArrow;</span></th>
                <th>Entrada<span class="icon-arrow">&UpArrow;</span></th>
                <th>Saída<span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach($presences as $presence)
                <tr>
                    <td>{{ $presence->created_at->format('d/m/Y') }}</td>

                    @foreach($user_shifts as $user_shift)
                        <td class="center-text"> {{ $user_shift->work_shift_id }} </td>
                    @endforeach

                    <td>
                        @if ($presence->first_start)
                            {{ Carbon\Carbon::parse($presence->first_start)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    <td>
                        @if ($presence->first_end)
                            {{ Carbon\Carbon::parse($presence->first_end)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    </td>

                    <td>
                        @if ($presence->second_start)
                            {{ Carbon\Carbon::parse($presence->second_start)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    </td>

                    <td>
                        @if ($presence->second_end)
                            {{ Carbon\Carbon::parse($presence->second_end)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
</main>
