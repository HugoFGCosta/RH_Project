@php
    use Carbon\Carbon;
@endphp
<main class="table" id="users_table">
    <section class="table__header">
        <h1>Lista de Usuários</h1>
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
                <th> Username <span class="icon-arrow">&UpArrow;</span></th>
                <th> Role <span class="icon-arrow">&UpArrow;</span></th>
                <th> Horário <span class="icon-arrow">&UpArrow;</span></th>
                <th> Detalhe/Editar <span class="icon-arrow">&UpArrow;</span></th>
                <th> Delete <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->role->role }}</td>
                    <td>
                        @if ($user->shift)
                            {{ 'Das ' . Carbon::parse($user->shift->work_shift->start_hour)->format('H:i') . ' às ' . Carbon::parse($user->shift->work_shift->end_hour)->format('H:i') }}
                        @else
                            O usuário não tem um turno de trabalho atribuído.
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('/user/edit', $user->id) }}" class="btn-detail-edit">Detalhe/Editar</a>
                    </td>
                    <td>
                        <form action="{{ url('/user/delete', $user->id) }}" method="POST" style="display:inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">Apagar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
</main>
