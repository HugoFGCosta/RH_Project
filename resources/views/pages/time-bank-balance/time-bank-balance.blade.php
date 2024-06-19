@extends('master.main')

@section('content')


    <div class="container pt-5">

        @component('components.time-bank-balance.time-bank-balance', ['month' => $month, 'year' => $year, 'bankFormattedFaltas'=>$bankFormattedFaltas, 'bankFormattedPresencas'=>$bankFormattedPresencas, 'bankTotal' => $bankTotal])

        @endcomponent

    </div>

@endsection
