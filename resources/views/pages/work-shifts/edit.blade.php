@extends('master.main')
@section('content')
    @component('components.work-shifts.work-shift-form-edit', ['workShift'=>$workshift])
    @endcomponent
@endsection
