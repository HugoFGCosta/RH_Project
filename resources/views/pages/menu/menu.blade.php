@extends('master.main')

@section('content')

    <div class="container">
        <h1>Escolha uma opção:</h1>

        <a href="/register-schedule"><button>Registo de horário</button></a>
        <a href="/dashboard-statistics"><button>Dashboard estatísticas</button></a>
        <a href="/view-absences"><button>Ver faltas</button></a>
        <a href="/manage-data"><button>Gerir dados</button></a>
        <a href="/vacation-plans"><button>Plano de férias</button></a>
        <a href="/approve-absence"><button>Aprovar Faltas</button></a>
    </div>




@endsection
