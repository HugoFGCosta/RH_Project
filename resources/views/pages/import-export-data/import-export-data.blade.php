@extends('master.main')

@section('content')

    <div class="container">
        <div class="row text-center">
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
                <a href="/register-schedule"><button>Importar Faltas</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="{{ route('exportAbsences') }}" class="btn btn-success">Exportar Faltas</a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Férias</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="{{ route('exportVacations') }}" class="btn btn-success">Exportar Férias</a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Presenças</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="{{ route('exportPresences') }}" class="btn btn-success">Exportar Presenças</a>
            </div>
        </div>
    </div>



@endsection
