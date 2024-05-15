<div class="form-container">
    <form method="POST" action="{{ route('register') }}">
        @method('POST')
        @csrf
        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                   value="{{ old('name') }}" required autocomplete="name" autofocus>
            <div class="form-underline"></div>
            @error('name')
            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('E-Mail Address') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required autocomplete="email">
            <div class="form-underline"></div>
            @error('email')
            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="new-password">
            <div class="form-underline"></div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                   autocomplete="new-password">
            <div class="form-underline"></div>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select name="role_id" id="role_id" class="form-control">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                @endforeach
            </select>
            <div class="form-underline"></div>
        </div>

        <div class="form-group">
            <label for="address">{{ __('Address') }}</label>
            <input id="address" type="text" class="form-control" name="address" required>
            <div class="form-underline"></div>
        </div>

        <div class="form-group">
            <label for="nif">{{ __('NIF') }}</label>
            <input id="nif" type="text" class="form-control" name="nif" required>
            <div class="form-underline"></div>
        </div>

        <div class="form-group">
            <label for="tel">{{ __('Tel') }}</label>
            <input id="tel" type="text" class="form-control" name="tel" required>
            <div class="form-underline"></div>
        </div>

        <div class="form-group">
            <label for="birth_date">{{ __('Birth Date') }}</label>
            <input id="birth_date" type="text" class="form-control" name="birth_date" required>
            <div class="form-underline"></div>
        </div>

        <div class="form-group">
            <label for="work_shift">{{ __('Work Shift') }}</label>
            <select name="work_shift_id" id="work_shift_id" class="form-control">
                @foreach ($work_shifts as $work_shift)
                    <option value="{{ $work_shift->id }}">
                        {{ $work_shift->start_hour . ' ~ ' . $work_shift->break_start . ' - ' . $work_shift->break_end . ' ~ ' . $work_shift->end_hour }}
                    </option>
                @endforeach
            </select>
            <div class="form-underline"></div>
        </div>

        <div class="form-group mb-0">
            <button type="submit" class="btn">{{ __('Register') }}</button>
        </div>
    </form>
</div>
