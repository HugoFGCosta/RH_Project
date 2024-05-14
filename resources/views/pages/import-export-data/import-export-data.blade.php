@extends('master.main')

@section('content')

    <div class="container">
        <div class="row text-center">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Verificar e exibir mensagens de erro --}}
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Exibir erros de validação de formulário --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <form action="{{ route('importUsers') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <button class="btn btn-primary">Importar Utilizadores</button>
                </form>
                <form action="{{route('exportUsers')}}" method = 'GET'>
                    @csrf
                    <button type="submit" class="btn btn-success mb-2">Exportar Utilizadores</button>
                </form>
            </div>
            <div>
                <form action="{{ route('importAbsences') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <button class="btn btn-primary">Importar Faltas</button>
                </form>
                <form action="{{route('exportAbsences')}}" method = 'GET'>
                    @csrf
                    <button type="submit" class="btn btn-success mb-2">Exportar Faltas</button>
                </form>
            </div>
            <div>
                <form action="{{ route('importVacations') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <button class="btn btn-primary">Importar Férias</button>
                </form>
                <form action="{{route('exportVacations')}}" method = 'GET'>
                    @csrf
                    <button type="submit" class="btn btn-success mb-2">Exportar Férias</button>
                </form>
            </div>
            <div>
                <form action="{{ route('importPresences') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <button class="btn btn-primary">Importar Presenças</button>
                </form>
                <form action="{{route('exportPresences')}}" method = 'GET'>
                    @csrf
                    <button type="submit" class="btn btn-success mb-2">Exportar Presenças</button>
                </form>
            </div>
         </div>

<<<<<<< HEAD
=======
            <div class="col-md-6 mb-3">
                <a href="{{ route('exportUsers') }}" class="btn btn-success">Exportar Utilizadores</a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button class="sub-menu">Importar Faltas</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="{{ route('exportAbsences') }}" class="btn btn-success">Exportar Faltas</a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button class="sub-menu">Importar Férias</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="{{ route('exportVacations') }}" class="btn btn-success">Exportar Férias</a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button class="sub-menu">Importar Presenças</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="{{ route('exportPresences') }}" class="btn btn-success">Exportar Presenças</a>
            </div>
>>>>>>> 7c5ede4a79865eb6065791e9fe4d9acd0749ad0c
    </div>
@endsection
