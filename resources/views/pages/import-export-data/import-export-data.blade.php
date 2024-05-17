@extends('master.main')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv">
        <button type="submit">Import Users</button>
    </form>
    <a href="{{ route('export') }}">Export Users</a>

    <form action="{{ route('importAbsences') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv">
        <button type="submit">Import Absences</button>
    </form>
    <a href="{{ route('exportAbsences') }}">Export Absences</a>

    <form action="{{ route('importVacations') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv">
        <button type="submit">Import Vacations</button>
    </form>
    <a href="{{ route('exportVacations') }}">Export Vacations</a>

    <form action="{{ route('importPresences') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv">
        <button type="submit">Import Presences</button>
    </form>
    <a href="{{ route('exportPresences') }}">Export Presences</a>

@endsection
