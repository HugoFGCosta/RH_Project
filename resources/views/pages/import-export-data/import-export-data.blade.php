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



@endsection
