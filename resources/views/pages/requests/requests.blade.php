@extends('master.main')

@section('content')
    <div class="container div-button">
        <a href="/view-absences"><button class="sub-menu">Ver faltas</button></a>
        <a href="//users/{{Auth::user()->id}}/edit"><button class="sub-menu">Gerir dados</button></a>
        <a href="{{ url('user/edit') }}" type="button"><button class="sub-menu">Gerir Dados</button></a>
        <a href="{{ url('user/show') }}" type="button"><button class="sub-menu">Mostrar Dados</button></a>
        <a href="/vacation-plans"><button class="sub-menu">Plano de férias</button></a>
        <a href="/approve-absence"><button class="sub-menu">Aprovar Faltas</button></a>
    </div>
@endsection
