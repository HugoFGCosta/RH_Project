@extends('master.main')

@component('components.styles.importsExports')
@endcomponent

@section('content')


    <div class="containerExcel">
        <h1 class="titleExcel">Exportação e Importação de dados</h1>

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

        <div class="firstContainer">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data" class="importForm">
                @csrf
                <input class="import-input" type="file" name="file" accept=".csv">
                <div class="buttonsDiv">
                    <button class="buttonImport" type="submit">Importar Users</button>
                    <a class="exportButton" href="{{ route('export') }}">Exportar Utilizadores</a>
                </div>

            </form>

            <form action="{{ route('importAbsences') }}" method="POST" enctype="multipart/form-data" class="importForm">
                @csrf
                <input class="import-input" type="file" name="file" accept=".csv">
                <div class="buttonsDiv">
                    <button class="buttonImport" type="submit">Importar Faltas</button>
                    <a class="exportButton" href="{{ route('exportAbsences') }}">Exportar Faltas</a>

                </div>
            </form>
        </div>

        <div class="firstContainer">
            <form action="{{ route('importVacations') }}" method="POST" enctype="multipart/form-data" class="importForm">
                @csrf
                <input class="import-input" type="file" name="file" accept=".csv">
                <div class="buttonsDiv">
                    <button class="buttonImport" type="submit">Importar Férias</button>
                    <a class="exportButton" href="{{ route('exportVacations') }}">Exportar Férias</a>

                </div>
            </form>

            <form action="{{ route('importPresences') }}" method="POST" enctype="multipart/form-data" class="importForm">
                @csrf
                <input class="import-input" type="file" name="file" accept=".csv">
                <div class="buttonsDiv">
                    <button class="buttonImport" type="submit">Importar Presenças</button>
                    <a class="exportButton" href="{{ route('exportPresences') }}">Exportar Presenças</a>
                </div>
            </form>
        </div>

        <div class="firstContainer">
            <form class="importForm">
                <div class="buttonsDiv">
                    <a class="exportButton" href="{{ route('exportWorkShifts') }}">Exportar Horários</a>
                </div>
            </form>
        </div>

        <div class="captionDiv">
            <h3 class="captionParagraph">Como importar</h3>
            <h4 class="captionParagraph">Para importar os dados os ficheiros excel devem seguir a seguinte estrutura</h4>
            <div class="legendaContainer">
                <p class="captionParagraph"><b>Utilizador:</b> <i>Id_role,Nome,Address,Nif,Telemóvel,Data_Nascimento,Email,Password,Id_horario</i></p>
                <p class="captionParagraph"><b>Faltas:</b> <i>Id_Utilizador,Id_Estado_Falta,Id_Utilizador_Que_Aprovou,Data_De_Falta,Justificação</i></p>
                <p class="captionParagraph"><b>Férias:</b> <i>Id_Utilizador,Id_Estado_Aprovação_Férias,Id_Utilizador_Que_Aprovou,Data_Inicio,Data_Fim</i></p>
                <p class="captionParagraph"><b>Presenças:</b> <i>Id_Utilizador,Primeira_Entrada,Primeira_Saída,Segunda_Entrada,Segunda_Saída,Horas_Extra,Horas_Efetivas</i></p>
            </div>
        </div>



    </div>



@endsection
