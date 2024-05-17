@if ($errors->any())
    <div class="alert alert-danger">
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
        <div class="input-data">
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ isset($user->name) ? $user->name : '' }}" required autocomplete="name" autofocus>
            <div class="underline"></div>
            <label for="name">Name</label>
            @error('name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-data">
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ isset($user->email) ? $user->email : '' }}" required autocomplete="email">
            <div class="underline"></div>
            <label for="email">Email</label>
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="input-data">
            @if (Auth::user()->role->role == 'Administrator')
                <select name="role" id="role" class="form-control" required>
                    <option value="" disabled {{ !isset($user->role->role) ? 'selected' : '' }}>Tipo de Usuário</option>
                    <option
                        value="Administrator" {{ isset($user->role->role) && $user->role->role == 'Administrator' ? 'selected' : '' }}>
                        Administrador
                    </option>
                    <option
                        value="Manager" {{ isset($user->role->role) && $user->role->role == 'Manager' ? 'selected' : '' }}>
                        Gestor
                    </option>
                    <option
                        value="Worker" {{ isset($user->role->role) && $user->role->role == 'Worker' ? 'selected' : '' }}>
                        Utilizador
                    </option>
                </select>
                <div class="underline"></div>
                <label for="role">Tipo de Usuário</label>
            @else
                <input type="hidden" name="role" value="{{ $user->role->role }}">
                <input type="text" class="readonly-input" value="{{ $user->role->role }}" readonly>
                <label for="role">Tipo de Usuário</label>
                <div class="underline"></div>
            @endif
        </div>
        <div class="input-data">
            <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                   value="{{ isset($user->address) ? $user->address : '' }}" required autocomplete="address">
            <div class="underline"></div>
            <label for="address">Endereço</label>
            @error('address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            <input type="text" id="nif" name="nif" class="form-control @error('address') is-invalid @enderror"
                   value="{{ isset($user->nif) ? $user->nif : '' }}" required autocomplete="address">
            <div class="underline"></div>
            <label for="nif">NIF</label>
            @error('NIF')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-data">
            <input type="text" id="tel" name="tel" class="form-control @error('tel') is-invalid @enderror"
                   value="{{ isset($user->tel) ? $user->tel : '' }}" required autocomplete="address">
            <div class="underline"></div>
            <label for="tel">Telefone</label>
            @error('tel')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="input-data">
            <input type="text" id="birth_date" name="birth_date"
                   class="form-control @error('birth_date') is-invalid @enderror"
                   value="{{ isset($user->birth_date) ? $user->birth_date : '' }}" required autocomplete="address">
            <div class="underline"></div>
            <label for="birth_date">Data de Nascimento</label>
            @error('birth_date')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-data">
            @if (Auth::user()->role->role == 'Administrator')
                <select name="work_shift_id" id="work_shift_id" class="form-control" required>
                    <option value="" disabled {{ !isset($user_shift->work_shift_id) ? 'selected' : '' }}></option>
                    @foreach ($work_shifts as $work_shift)
                        <option
                            value="{{ $work_shift->id }}" {{ isset($user_shift->work_shift_id) && $user_shift->work_shift_id == $work_shift->id ? 'selected' : '' }}>
                            {{ $work_shift->start_hour . ' ~ ' . $work_shift->break_start . ' - ' . $work_shift->break_end . ' ~ ' . $work_shift->end_hour }}
                        </option>
                    @endforeach
                </select>
                <div class="underline"></div>
                <label for="work_shift_id">{{ __('Work Shift') }}</label>
            @else
                @foreach ($work_shifts as $work_shift)
                    @if ($user_shift->work_shift_id == $work_shift->id)
                        <input type="hidden" name="work_shift_id" value="{{ $work_shift->id }}">
                        <input type="text" class="readonly-input"
                               value="{{ $work_shift->start_hour . ' ~ ' . $work_shift->break_start . ' - ' . $work_shift->break_end . ' ~ ' . $work_shift->end_hour }}"
                               readonly>
                        <label for="work_shift_id">{{ __('Work Shift') }}</label>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <div class="form-row">
        <button type="submit" class="btn showform-btn">
            <span>{{ __('Guarde') }}</span>
        </button>
    </div>
</form>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
