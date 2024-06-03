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
                <th scope="col">Actions</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($user_shifts as $user_shift)
                <tr>
                    <th scope="row"> {{ $user_shift->id }} </th>
                    <td>{{ $user_shift->user->name }}</td>
                    <td>{{ $user_shift->work_shift->start_hour }} ~ {{ $user_shift->work_shift->end_hour }}</td>
                    <td>
                        <a href="{{ url('/users/shift-list/edit/' . $user_shift->id) }}">EDIT</a>
                        -
                        <form method="POST" action="{{ route('user_shift.destroy', $user_shift) }}"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="background: none!important; border: none; padding: 0!important; color: #069; text-decoration: underline; cursor: pointer;">DELETE</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
