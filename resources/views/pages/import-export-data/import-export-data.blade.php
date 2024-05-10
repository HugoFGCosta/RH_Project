@extends('master.main')

@section('content')

    <div class="container">
        <div class="row text-center">
            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Utilizadores</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="/register-schedule"><button>Exportar Utilizadores</button></a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Faltas</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="/register-schedule"><button>Exportar Faltas</button></a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Férias</button></a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="/register-schedule"><button>Exportar Férias</button></a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button>Importar Presenças</button></a>
            </div>

            <div class="col-md-6">
                <a href="/register-schedule"><button>Exportar Presenças</button></a>
            </div>
        </div>
    </div>



@endsection
