@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="Form recoverPassword-form">
            {{--                    <div class="card-header">{{ __('Reset Password') }}</div>--}}
            <img src="{{ asset('images/hospital.svg') }}" alt="" style="width: 100%; height: auto;" class="p-2 mt-5">
            <h4 class="mt-5 mb-4">Recuperar Palavra-Passe</h4>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="input-container mt-4 align-items-start">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                <label for="email">{{ __('Email') }}</label>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>




                                <div class="input-container mt-5">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    <label for="password">{{ __('Nova Password') }}</label>
                                    <img src="{{ asset('images/eye-closed.png') }}" id="eyeicon">
                                </div>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                <div class="input-container mt-5">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    <label for="password-confirm">{{ __('Confirmar Password') }}</label>
                                    <img src="{{ asset('images/eye-closed.png') }}" id="eyeicon">
                                </div>


                                    <button type="submit" class="btn">
                                        {{ __('Confirmar') }}
                                    </button>
                        </form>
                </div>
            </div>
    </div>
@endsection


<!--
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="recoverPassword.css">

</head>

<body>
<div class="container">
    <div class="Form recoverPassword-form">
        <img src="logos/hospital.svg" alt="" style="width: 100%; height: auto;" class="p-2 mb-4 mt-0">
        <h4 class="mt-5 mb-4">Recuperar Palavra-Passe</h2>
            <form action="#">
                <div class="input-container mt-5">
                    <input type="password" required="" id="password" />
                    <label>Nova Palavra-Passe</label>
                    <img src="icons/eye-closed.png" id="eyeicon">
                </div>
                <div class="input-container mt-5">
                    <input type="password" required="" id="passwordConfirmar" />
                    <label>Confirmar Palavra-Passe</label>
                    <img src="icons/eye-closed.png" id="eyeicon2">
                </div>

                <button class="btn mt-5">Confirmar</button>
            </form>
            <p class="p p-3 m-2">Já tem uma conta? <a href="Login.html">Iniciar Sessão</a></p>
    </div>
</div>

<script src="recoverPassword.js"></script>
</body>


</html>
-->
