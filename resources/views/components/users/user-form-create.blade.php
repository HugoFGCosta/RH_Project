@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif




{{-- <form method="POST" action="{{ route('register') }}"> --}}

<form method="POST" action="{{ route('admin-register') }}">
    @method('POST')
    @csrf
    <div class="form-row">
        <div class="input-data">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                   value="{{ old('name') }}" required autocomplete="name" autofocus>
            <div class="underline"></div>
            <label for="name">{{ __('Name') }}</label>
            @error('name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-data">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required autocomplete="email">
            <div class="underline"></div>
            <label for="email">{{ __('E-Mail Address') }}</label>
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="new-password">
            <div class="underline"></div>
            <label for="password">{{ __('Password') }}</label>
            @error('password')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-data">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                   autocomplete="new-password">
            <div class="underline"></div>
            <label for="password-confirm">{{ __('Confirm Password') }}</label>
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            <select name="role_id" id="role_id" class="form-control" required>
                <option value="" disabled selected></option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                @endforeach
            </select>
            <div class="underline"></div>
            <label for="role_id">Role</label>
        </div>
        <div class="input-data">
            <input id="address" type="text" class="form-control" name="address" required>
            <div class="underline"></div>
            <label for="address">{{ __('Address') }}</label>
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            <input id="nif" type="text" class="form-control" name="nif" required>
            <div class="underline"></div>
            <label for="nif">{{ __('NIF') }}</label>
        </div>
        <div class="input-data">
            <input id="tel" type="text" class="form-control" name="tel" required>
            <div class="underline"></div>
            <label for="tel">{{ __('Tel') }}</label>
        </div>
    </div>

    <div class="form-row">
        <div class="input-data">
            <input id="birth_date" type="text" class="form-control" name="birth_date" required>
            <div class="underline"></div>
            <label for="birth_date">{{ __('Birth Date') }}</label>
        </div>
        <div class="input-data">
            <select name="work_shift_id" id="work_shift_id" class="form-control" required>
                <option value="" disabled selected></option>
                @foreach ($work_shifts as $work_shift)
                    <option value="{{ $work_shift->id }}">
                        {{ $work_shift->start_hour . ' ~ ' . $work_shift->break_start . ' - ' . $work_shift->break_end . ' ~ ' . $work_shift->end_hour }}
                    </option>
                @endforeach
            </select>
            <div class="underline"></div>
            <label for="work_shift_id">{{ __('Work Shift') }}</label>
        </div>
    </div>

    <div class="form-row">
        <button type="submit" class="btn showform-btn">
            <span>{{ __('Registe') }}</span>
        </button>
    </div>
</form>
