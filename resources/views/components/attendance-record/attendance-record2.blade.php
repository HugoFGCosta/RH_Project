@php
    use Carbon\Carbon;
@endphp
<main class="table" id="users_table">
    <section class="table__header">
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
                @php
                    $currentShift = $user_shifts->filter(function($shift) use ($presence) {
                        return Carbon::parse($shift->start_date)->lte(Carbon::parse($presence->created_at)) &&
                               (is_null($shift->end_date) || Carbon::parse($shift->end_date)->gte(Carbon::parse($presence->created_at)));
                    })->first();
                @endphp
                <tr>
                    <td>{{ $presence->created_at->format('d/m/Y') }}</td>
                    <td class="center-text">
                        @if ($currentShift)
                            {{ Carbon::parse($currentShift->work_shift->start_hour)->format('H:i') }} - {{ Carbon::parse($currentShift->work_shift->end_hour)->format('H:i') }}
                        @else
                            <span class="red-text">Sem Escala</span>
                        @endif
                    </td>
                    <td>
                        @if ($presence->first_start)
                            {{ Carbon::parse($presence->first_start)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    </td>
                    <td>
                        @if ($presence->first_end)
                            {{ Carbon::parse($presence->first_end)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    </td>
                    <td>
                        @if ($presence->second_start)
                            {{ Carbon::parse($presence->second_start)->format('H:i:s') }}
                        @else
                            <span class="red-text">Sem Registo</span>
                        @endif
                    </td>
                    <td>
                        @if ($presence->second_end)
                            {{ Carbon::parse($presence->second_end)->format('H:i:s') }}
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
