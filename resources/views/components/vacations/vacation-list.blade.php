
<div class="container mt-5">
    <h1>vacations List</h1>

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
                <th scope="col">#</th>
                <th scope="col">Id</th>
                <th scope="col">Nome</th>
                <th scope="col">Estado</th>
                <th scope="col">Aprovado por</th>
                <th scope="col">De</th>
                <th scope="col">Até</th>
            </tr>
        </thead>
        <tbody>
        <a href="vacations/create"><button>Nova vacation</button></a>
 @foreach($vacations as $vacation)
                <tr>
                    <td>{{ $vacation->id }}</td>
                    <td>{{ $vacation->user_id}}</td>
                    <td>{{ $vacation->user->name}}</td>
                    <td>{{$vacation->vacation_approval_states_id}}</td>

                    <td>{{ $vacation->date_start}}</td>
                    <td>{{ $vacation->date_end }}</td>

                    <td>
                        @auth
                            <a href="{{url('vacations/'.'edit/' . $vacation->id  )}}" type="button" class="btn btn-primary">Edit</a></td>
                        <form  action="{{url('vacations/' .'delete/' .$vacation->id)}}" method="POST">
                        @csrf
                              @method('DELETE')
                            <td><button type="submit" class="btn btn-primary">Delete</button>
                        </form>
                        @endauth
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div style="" class="">
        {{ $vacations->links() }}
    </div>
</div>
