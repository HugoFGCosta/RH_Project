@extends('master.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1>Aprovar Justificação</h1>
                @component('components.justifications.justification-approve', [
                    'justification' => $justification,
                    'duration'=> $duration,
                ])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
