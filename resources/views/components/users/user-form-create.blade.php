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
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                value="{{ old('name') }}" required autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

        <div class="col-md-6">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required autocomplete="email">

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

        <div class="col-md-6">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="new-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label for="password-confirm"
            class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

        <div class="col-md-6">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                autocomplete="new-password">
        </div>
    </div>

    <div class="form-group">
        <label for="role">ROLE</label>
        <select name="role_id" id="role_id" class="form-control">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->role }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group row">
        <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('address') }}</label>

        <div class="col-md-6">
            <input id="address" type="text" class="form-control" name="address" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="nif" class="col-md-4 col-form-label text-md-right">{{ __('nif') }}</label>

        <div class="col-md-6">
            <input id="nif" type="text" class="form-control" name="nif" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="tel" class="col-md-4 col-form-label text-md-right">{{ __('tel') }}</label>

        <div class="col-md-6">
            <input id="tel" type="text" class="form-control" name="tel" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="birth_date" class="col-md-4 col-form-label text-md-right">{{ __('birth date') }}</label>

        <div class="col-md-6">
            <input id="birth_date" type="text" class="form-control" name="birth_date" required>
        </div>
    </div>


    <div class="form-group">
        <label for="work_shift">work_shift</label>
        <select name="work_shift_id" id="work_shift_id" class="form-control">
            @foreach ($work_shifts as $work_shift)
                <option value="{{ $work_shift->id }}">
                    {{ $work_shift->start_hour . ' ~ ' . $work_shift->break_start . ' - ' . $work_shift->break_end . ' ~ ' . $work_shift->end_hour }}
                </option>
            @endforeach
        </select>
    </div>




    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Register') }}
            </button>
        </div>
    </div>
    <br>
    </div>
</form>
