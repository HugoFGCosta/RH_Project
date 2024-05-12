@extends('master.main')

@section('content')

    <div class="container">
        <div class="row text-center">
            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Faltas</button></a>
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
