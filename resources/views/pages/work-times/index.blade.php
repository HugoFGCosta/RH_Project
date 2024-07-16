@extends('master.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/work-times.css') }}">
@endsection
@php
    use Carbon\Carbon;
    // Generate arrays for months and years
    $months = [
        'Janeiro' => '01', 'Fevereiro' => '02', 'Março' => '03', 'Abril' => '04',
        'Maio' => '05', 'Junho' => '06', 'Julho' => '07', 'Agosto' => '08',
        'Setembro' => '09', 'Outubro' => '10', 'Novembro' => '11', 'Dezembro' => '12'
    ];
    $years = range(Carbon::now()->year, Carbon::now()->year - 10);
@endphp
@section('content')
    @if (session('success') || session('error'))
        <div id="modal-container" class="modal-container">
            <div class="modal">
                <span class="close-btn" id="close-btn">&times;</span>
                <div class="modal-content">
                    @if (session('success'))
                        <div class="alert success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert error">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <h1>Períodos de Trabalho</h1>
    <div class="centralBox">

        <div class="table__header input-group-wrapper">
            <div class="input-group">
                <input type="search" id="searchInput" placeholder="Pesquisar...">
            </div>
            <!-- Month Filter -->
            <div class="input-group-filter no-export">
                <select id="monthFilter">
                    <option value="">Selecionar Mês</option>
                    @foreach($months as $month => $value)
                        <option value="{{ $month }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Year Filter -->
            <div class="input-group-filter no-export">
                <select id="yearFilter">
                    <option value="">Selecionar Ano</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
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
                        <tr data-date="{{ $userShift->start_date }}">
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
                            <td>
                                <button class="btn btn-primary openModal" data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}">Adicionar Turno
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="workTimeModal" class="modal-work-times">
        <div class="modal-content-work-times">
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
