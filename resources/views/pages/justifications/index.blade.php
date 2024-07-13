@extends('master.main')

@section('content')

    @component('components.alerts.alerts')
    @endcomponent

    <div class="container pt-5">
        <h1>Lista de justificações</h1>
        @component('components.justifications.justification-list', ['justifications' => $justifications])
        @endcomponent
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/justification-list.js') }}"></script>
@endsection
