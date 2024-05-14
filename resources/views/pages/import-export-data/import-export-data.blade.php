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

    </div>



@endsection
