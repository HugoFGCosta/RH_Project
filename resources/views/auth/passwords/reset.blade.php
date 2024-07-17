@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="Form recoverPassword-form">
            <img src="{{ asset('images/hospital.svg') }}" alt="" style="width: 100%; height: auto;" class="p-2 mt-5">
            <h4 class="mt-5 mb-4">Recuperar Palavra-Passe</h4>
            <form method="POST" action="{{ route('password.update') }}" class="form-container">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-container mt-4 align-items-start">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    <label for="email">{{ __('Email') }}</label>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="input-container mt-5">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password">
                    <label for="password">{{ __('Nova Password') }}</label>
                    <img src="{{ asset('images/eye-closed.png') }}" id="password-eyeicon">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="input-container mt-5">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                           autocomplete="new-password">
                    <label for="password-confirm">{{ __('Confirmar Password') }}</label>
                    <img src="{{ asset('images/eye-closed.png') }}" id="confirm-password-eyeicon">
                </div>

                <button type="submit" class="btn btn-submit">
                    {{ __('Confirmar') }}
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/recover.js') }}"></script>
@endsection
