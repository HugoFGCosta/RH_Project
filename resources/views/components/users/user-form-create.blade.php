<form method="POST" action="{{ route('admin-register') }}">
    @method('POST')
    @csrf

    @if ($errors->any())
        <div class="error-register">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-row">
        <div class="input-data full-width">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                value="{{ old('name') }}" required autocomplete="name" autofocus>
            <div class="underline"></div>
            <label for="name">{{ __('Nome*') }}</label>
            @error('name')
            @enderror
        </div>

        <div class="input-data">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required autocomplete="email">
            <div class="underline"></div>
            <label for="email">{{ __('E-Mail*') }}</label>
            @error('email')
            @enderror
        </div>

        <div class="input-data">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" value="{{ old('password') }}" required autocomplete="new-password">
            <div class="underline"></div>
            <label for="password">{{ __('Palavra-Passe*') }}</label>
            @error('password')
            @enderror
        </div>

        <div class="input-data">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                value="{{ old('password_confirmation') }}" required autocomplete="new-password">
            <div class="underline"></div>
            <label for="password-confirm">{{ __('Confirmar Palavra-Passe*') }}</label>
        </div>

        <div class="input-data">
            <select name="role_id" id="role_id" class="form-control" value="{{ 'role_id' }}" required>
                <option value="" disabled selected></option>
                @foreach ($roles as $role)
                    <option @if (old('role_id') == $role->id) {{ 'selected="selected"' }} @endif
                        value="{{ $role->id }}">
                        @if ($role->role == 'Worker')
                            Utilizador
                        @elseif($role->role == 'Manager')
                            Gestor
                        @elseif($role->role == 'Administrator')
                            Administrador
                        @endif
                    </option>
                @endforeach
            </select>
            <div class="underline"></div>
            <label for="role_id">Função</label>
        </div>

        <div class="input-data full-width">
            <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}"
                required>
            <div class="underline"></div>
            <label for="address">{{ __('Morada*') }}</label>
        </div>

        <div class="input-data">
            <input id="nif" type="text" class="form-control" name="nif" value="{{ old('nif') }}"
                required>
            <div class="underline"></div>
            <label for="nif">{{ __('Número Identificação Fiscal (NIF)*') }}</label>
        </div>

        <div class="input-data">
            <input id="tel" type="text" class="form-control" name="tel" value="{{ old('tel') }}"
                required>
            <div class="underline"></div>
            <label for="tel">{{ __('Telemóvel*') }}</label>
        </div>

        <div class="input-data">
            <input id="birth_date" type="date" class="form-control" name="birth_date"
                value="{{ old('birth_date') }}" required>
            <div class="underline"></div>
            <label for="birth_date">{{ __('Data Nascimento*') }}</label>
        </div>

        <div class="input-data">
            <select name="work_shift_id" id="work_shift_id" class="form-control" value="{{ old('work_shift_id') }}"
                required>
                <option value="" disabled selected></option>
                @foreach ($work_shifts as $work_shift)
                    <option @if (old('work_shift_id') == $work_shift->id) {{ 'selected="selected"' }} @endif
                        value="{{ $work_shift->id }}">
                        {{ \Carbon\Carbon::parse($work_shift->start_hour)->format('H\hi') . ' / ' . \Carbon\Carbon::parse($work_shift->break_start)->format('H\hi') . ' - ' . \Carbon\Carbon::parse($work_shift->break_end)->format('H\hi') . ' / ' . \Carbon\Carbon::parse($work_shift->end_hour)->format('H\hi') }}
                    </option>
                @endforeach
            </select>
            <div class="underline"></div>
            <label for="work_shift_id">{{ __('Horário de Trabalho*') }}</label>
        </div>
    </div>
    <p>Campos com asterico(*) são obrigatórios.</p>
    <div class="form-row">
        <button type="submit" class="btn showform-btn">
            <span>{{ __('Confirmar') }}</span>
        </button>
    </div>
</form>
