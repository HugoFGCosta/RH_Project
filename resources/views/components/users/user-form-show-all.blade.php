@php
    use Carbon\Carbon;
@endphp
<ul style="list-style-type: none; border: 1px solid black;">
    @foreach ($users as $user)
        <li style="{{ $loop->iteration % 2 == 0 ? 'background-color: #f2f2f2;' : '' }}">
            <p>Nome: {{ $user->name }}</p>
            <p>Email: {{ $user->email }}</p>
            <p>Cargo: {{ $user->role->role }}</p>
            <p>Endereço: {{ $user->address }}</p>
            <p>NIF: {{ $user->nif }}</p>
            <p>TEL: {{ $user->tel }}</p>
            <p>Data de Nascimento: {{ $user->birth_date }}</p>
            <p>Horário de Trabalho:
                @if ($user->shift)
                    {{ 'Das ' . Carbon::parse($user->shift->work_shift->start_hour)->format('H:i') . ' às ' . Carbon::parse($user->shift->work_shift->end_hour)->format('H:i') }}
                @else
                    O usuário não tem um turno de trabalho atribuído.
                @endif
            </p>
            <a href="{{ url('/user/edit', $user->id) }}">Editar</a>
        </li>
    @endforeach
</ul>
