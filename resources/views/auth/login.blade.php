@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="Form login-form">
            <img src="{{ asset('images/hospital.svg') }}" alt="" style="width: 70%; height: auto;" class="p-2 mt-5">
            <h4 class="mt-5 mb-3">Login</h4>
            <p class="p lead p-3 m-2">Bem-vindo! Por favor, inicie sessão para aceder à sua conta.</p>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-container mt-4 align-items-start ">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus/>
                    <label for="email">Email</label>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="input-container mt-5">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="current-password"/>
                    <label for="password">Palavra-Passe</label>
                    <img src="{{ asset('images/eye-closed.png') }}" id="eyeicon">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button type="submit" class="btn">
                    {{ __('Entrar') }}
                </button>

                @if (Route::has('password.request'))
                    <p class="p p-3 m-2"><a href="{{ route('password.request') }}">Recuperar Palavra-Passe</a></p>
                @endif
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/login.js') }}"></script>
@endsection
