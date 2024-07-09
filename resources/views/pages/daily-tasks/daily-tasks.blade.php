@extends('master.main')

@section('content')
    <div class="container div-button">
        <a href="/register-schedule"><button class="sub-menu">Registo de horário</button></a>
        <a href="/dashboard-statistics"><button class="sub-menu">Dashboard estatísticas</button></a>
        <a href="/attendance-record"><button class="sub-menu">Registo de Assiduidade</button></a>

        @if (session('success'))
            <div class="alert alert-success successMessage">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger errorMessage">
                {{ session('error') }}
            </div>
        @endif

        <a href="/export/work-shifts/{{Auth::user()->id}}"><button class="sub-menu">Exportar horário deste User</button></a>
    </div>
@endsection
