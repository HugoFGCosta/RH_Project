@if ($errors->any())
    <div class="error-register">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ url('user/edit') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-row">
        <div class="input-data full-width">
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ isset($user->name) ? $user->name : '' }}" required autocomplete="name" autofocus>
            <div class="underline"></div>
            <label for="name">Nome*</label>
        </div>

        <div class="input-data full-width">
            <input type="text" id="address" name="address"
                class="form-control @error('address') is-invalid @enderror"
                value="{{ isset($user->address) ? $user->address : '' }}" required autocomplete="address">
            <div class="underline"></div>
            <label for="address">Endereço*</label>
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            <input type="email" id="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ isset($user->email) ? $user->email : '' }}" required autocomplete="email">
            <div class="underline"></div>
            <label for="email">E-Mail*</label>
        </div>

        <div class="input-data">
            <input type="text" id="nif" name="nif" class="form-control @error('nif') is-invalid @enderror"
                value="{{ isset($user->nif) ? $user->nif : '' }}" required autocomplete="nif">
            <div class="underline"></div>
            <label for="nif">Número de Identificação Fiscal (NIF)*</label>
        </div>

        <div class="input-data">
            <input type="text" id="tel" name="tel" class="form-control @error('tel') is-invalid @enderror"
                value="{{ isset($user->tel) ? $user->tel : '' }}" required autocomplete="tel">
            <div class="underline"></div>
            <label for="tel">Telefone*</label>
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            @if (Auth::user()->role->role == 'Administrator')
                <select name="role" id="role" class="form-control" required>
                    <option value="" disabled {{ !isset($user->role->role) ? 'selected' : '' }}>Tipo de
                        Utilizador*
                    </option>
                    <option value="Administrator"
                        {{ isset($user->role->role) && $user->role->role == 'Administrator' ? 'selected' : '' }}>
                        Administrador
                    </option>
                    <option value="Manager"
                        {{ isset($user->role->role) && $user->role->role == 'Manager' ? 'selected' : '' }}>
                        Gestor
                    </option>
                    <option value="Worker"
                        {{ isset($user->role->role) && $user->role->role == 'Worker' ? 'selected' : '' }}>
                        Utilizador
                    </option>
                </select>
                <div class="underline"></div>
                <label for="role">Tipo de Utilizador*</label>
            @else
                <input type="hidden" name="role" value="{{ $user->role->role }}">
                <input type="text" class="readonly-input" value="{{ $user->role->role }}" readonly>
                <label for="role">Tipo de Utilizador</label>
                <div class="underline"></div>
            @endif
        </div>

        <div class="input-data">
            <input type="date" id="birth_date" name="birth_date"
                class="form-control @error('birth_date') is-invalid @enderror"
                value="{{ isset($user->birth_date) ? $user->birth_date : '' }}" required autocomplete="birth_date">
            <div class="underline"></div>
            <label for="birth_date">Data de Nascimento*</label>
        </div>

        <div class="input-data">
            @if (Auth::user()->role->role == 'Administrator')
                <select name="work_shift_id" id="work_shift_id" class="form-control" required>
                    <option value="" disabled {{ !isset($user_shift->work_shift_id) ? 'selected' : '' }}>
                    </option>
                    @foreach ($work_shifts as $work_shift)
                        <option value="{{ $work_shift->id }}"
                            {{ isset($user_shift->work_shift_id) && $user_shift->work_shift_id == $work_shift->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($work_shift->start_hour)->format('H\hi') . ' / ' . \Carbon\Carbon::parse($work_shift->break_start)->format('H\hi') . ' - ' . \Carbon\Carbon::parse($work_shift->break_end)->format('H\hi') . ' / ' . \Carbon\Carbon::parse($work_shift->end_hour)->format('H\hi') }}
                        </option>
                    @endforeach
                </select>
                <div class="underline"></div>
                <label for="work_shift_id">{{ __('Horário de Trabalho*') }}</label>
            @else
                @foreach ($work_shifts as $work_shift)
                    @if ($user_shift->work_shift_id == $work_shift->id)
                        <input type="hidden" name="work_shift_id" value="{{ $work_shift->id }}">
                        <input type="text" class="readonly-input"
                            value="{{ \Carbon\Carbon::parse($work_shift->start_hour)->format('H\hi') . ' / ' . \Carbon\Carbon::parse($work_shift->break_start)->format('H\hi') . ' - ' . \Carbon\Carbon::parse($work_shift->break_end)->format('H\hi') . ' / ' . \Carbon\Carbon::parse($work_shift->end_hour)->format('H\hi') }}"
                            readonly>
                        <label for="work_shift_id">{{ __('Horário de Trabalho*') }}</label>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <p>Campos com asterisco(*) são obrigatórios.</p>
    <div class="form-row">
        <button type="submit" class="btn showform-btn">
            <span>{{ __('Guardar') }}</span>
        </button>
    </div>
</form>
