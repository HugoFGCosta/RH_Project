@extends('master.main')

@section('content')
    <div class="container div-button">
        <a href="/import-export-data"><button class="sub-menu">Exportar/Dados</button></a>
        <a href="/work-shifts"><button class="sub-menu">Horários</button></a>
        <a href="/admin-register"><button class="sub-menu">Criar Funcionário</button></a>
        <a href="/users/show-all"><button class="sub-menu">Listar Funcionários</button></a>
    </div>
@endsection
