@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="Form login-form">
            <img src="{{ asset('images/hospital.svg') }}" alt="" style="width: 70%; height: auto;" class="p-2 mb-5 mt-3">
            <h4 class="mt-3 mb-4">Recuperar Palavra-Passe</h4>
            <p class="p lead p-3 m-2">Por favor, forneça o seu e-mail para procedermos com a recuperação da sua conta.</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-container mt-4 align-items-start">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    <label for="email">{{ __('Email') }}</label>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn">
                    {{ __('Enviar') }}
                </button>

                <p class="p p-3 m-2">Já tem conta?  <a href="{{ route('login') }}">Iniciar Sessão</a></p>
            </form>
        </div>
    </div>
@endsection

