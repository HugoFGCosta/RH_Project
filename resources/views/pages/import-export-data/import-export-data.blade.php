@extends('master.main')

@section('content')

    <div class="container">
        <div class="row text-center">
<<<<<<< LuisBranch
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
=======
            <div>
                <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="messages">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                    <div class="fields">
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="import_csv" name="import_csv" accept=".csv">
                            <label class="input-group-text" for="import_csv">Upload</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Import CSV</button>
                </form>
            </div>
         </div>

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
>>>>>>> MergeTeste
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
<<<<<<< LuisBranch
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
=======

            <div class="col-md-6">
                <a href="/register-schedule"><button class="sub-menu">Importar Presenças</button></a>
>>>>>>> MergeTeste
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
@endsection

