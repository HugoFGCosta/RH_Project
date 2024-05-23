@extends('master.main')
@section('content')
    @component('components.work-shifts.work-shift-form-show', ['work_shift'=>$work_shift])
    @endcomponent
@endsection
