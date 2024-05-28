{{-- NAO ESTA PRONTO --}}


<div class="container mt-5">
    <h1>Lista USER Especifico</h1>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Work Shift</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user_shifts as $user_shift)
                <tr>
                    <th scope="row"> {{ $user_shift->id }} </th>
                    <td>{{ $user_shift->user->name }}</td>
                    <td>{{ $user_shift->work_shift->start_hour }} ~ {{ $user_shift->work_shift->end_hour }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
