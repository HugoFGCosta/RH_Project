@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/work-times.css') }}">
@endsection

@section('content')
    <div class="centralBox">
        <h1>Períodos de Trabalho</h1>

        @component('components.alerts.alerts')
        @endcomponent

        <div class="table__header">
            <div class="input-group">
                <input type="search" placeholder="Pesquisar...">
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Exportar como &nbsp; &#10140;</label>
                    <label for="export-file" id="toPDF">PDF</label>
                    <label for="export-file" id="toJSON">JSON</label>
                    <label for="export-file" id="toCSV">CSV</label>
                    <label for="export-file" id="toEXCEL">EXCEL</label>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table" id="work_times_table">
                <thead>
                <tr>
                    <th>Utilizador <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Turno <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Data de Início <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Data de Fim <span class="icon-arrow">&UpArrow;</span></th>
                    <th>Ações <span class="icon-arrow">&UpArrow;</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    @foreach ($user->user_shifts as $userShift)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>
                                @if($userShift->work_shift)
                                    {{ $userShift->work_shift->start_hour }} - {{ $userShift->work_shift->end_hour }}
                                @else
                                    No Shift
                                @endif
                            </td>
                            <td>{{ $userShift->start_date }}</td>
                            <td>{{ $userShift->end_date ?? 'Indefinido' }}</td>
                            <td><button class="btn btn-primary openModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">Adicionar Turno</button></td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="workTimeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h1>Adicionar Turno de Trabalho para <span id="modal_user_name"></span></h1>
            <form action="{{ route('work-times.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="modal_user_id">
                <div class="form-group">
                    <label for="work_shift_id">Turno</label>
                    <select name="work_shift_id" id="work_shift_id" class="form-control">
                        @foreach ($workShifts as $workShift)
                            <option value="{{ $workShift->id }}">
                                {{ $workShift->start_hour }} - {{ $workShift->end_hour }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Data de Início</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="end_date">Data de Fim</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Adicionar Turno</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/work-times.js') }}"></script>
@endsection
