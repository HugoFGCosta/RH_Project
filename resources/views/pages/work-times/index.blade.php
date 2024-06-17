@extends('master.main')

@section('content')
    <div class="container">
        <h1>Work Times</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
            <tr>
                <th>Employee</th>
                <th>Work Shifts</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>
                        @foreach ($user->user_shifts as $userShift)
                            @if($userShift->work_shift)
                                {{ $userShift->work_shift->start_hour }} - {{ $userShift->work_shift->end_hour }} ({{ $userShift->start_date }} - {{ $userShift->end_date }})
                            @else
                                ({{ $userShift->start_date }} - {{ $userShift->end_date }})
                            @endif
                            <br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h2>Add Work Time</h2>
        <form action="{{ route('work-times.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="user_id">Employee</label>
                <select name="user_id" id="user_id" class="form-control">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="work_shift_id">Work Shift</label>
                <select name="work_shift_id" id="work_shift_id" class="form-control">
                    @foreach ($workShifts as $workShift)
                        <option value="{{ $workShift->id }}">
                            {{ $workShift->start_hour }} - {{ $workShift->end_hour }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control">
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Work Time</button>
        </form>
    </div>
@endsection
