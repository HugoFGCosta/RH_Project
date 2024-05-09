{{-- NAO ESTA PRONTO --}}


{{-- <form method="POST" action="{{ url('users/' . $user->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" autocomplete="name" placeholder="Type your name"
            class="form-control
         @error('name') is-invalid @enderror" value="{{ $user->name }}" required
            aria-describedby="nameHelp">
        <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>


    <div class="form-group row">
        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

        <div class="col-md-6">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ $user->email }}" required autocomplete="email">

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
                name="password" required autocomplete="new-password" disabled>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
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


    <div class="form-group">
        <label for="name">Address</label>
        <input type="text" id="address" name="address" autocomplete="name" placeholder="Type your address"
            class="form-control
         @error('address') is-invalid @enderror" value="{{ $user->address }} " required
            aria-describedby="nameHelp">
        <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>


    <div class="form-group row">
        <label for="nif" class="col-md-4 col-form-label text-md-right">{{ __('nif') }}</label>

        <div class="col-md-6">
            <input id="nif" type="text" class="form-control" name="nif" value="{{ $user->nif }}"
                required>
        </div>
    </div>


    <div class="form-group row">
        <label for="tel" class="col-md-4 col-form-label text-md-right">{{ __('tel') }}</label>

        <div class="col-md-6">
            <input id="tel" type="text" class="form-control" name="tel" value="{{ $user->tel }}"
                required>
        </div>
    </div>


    <div class="form-group row">
        <label for="birth_date" class="col-md-4 col-form-label text-md-right">{{ __('birth date') }}</label>

        <div class="col-md-6">
            <input id="birth_date" type="text" class="form-control" name="birth_date"
                value="{{ $user->birth_date }}" required>
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


    <br>


    <button type="submit" class="mt-2 mb-5 btn btn-primary">Edit</button>


</form> --}}
