@extends('master.main')

@component('components.styles.importsExports')
@endcomponent

@section('content')

    <h1 class="titleExcel">Exportação e Importação de dados
        <span class="help-icon" id="openModal">?</span>
    </h1>

    <div class="containerExcel">
        @component('components.alerts.alerts')
        @endcomponent

        <div class="firstContainer">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data" class="importForm">
                @csrf
                <input class="import-input" type="file" name="file" accept=".csv">
                <div class="buttonsDiv">
                    <button class="buttonImport" type="submit">Importar Utilizadores</button>
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
    </div>

    <!-- Modal -->
    <div id="helpModal" class="modal-export">
        <div class="modal-export-content">
            <span class="close">&times;</span>
            <h2>Como importar</h2>
            <p>Para importar os dados os ficheiros excel devem seguir a seguinte estrutura:</p>
            <p><b>Utilizador:</b> <i>Id_role,Nome,Address,Nif,Telemóvel,Data_Nascimento,Email,Password,Id_horario</i></p>
            <p><b>Faltas:</b> <i>Id_Utilizador,Id_Estado_Falta,Id_Utilizador_Que_Aprovou,Data_De_Falta,Justificação</i></p>
            <p><b>Férias:</b> <i>Id_Utilizador,Id_Estado_Aprovação_Férias,Id_Utilizador_Que_Aprovou,Data_Inicio,Data_Fim</i></p>
            <p><b>Presenças:</b> <i>Id_Utilizador,Primeira_Entrada,Primeira_Saída,Segunda_Entrada,Segunda_Saída,Horas_Extra,Horas_Efetivas</i></p>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/imports-exports.js') }}"></script>
@endsection
