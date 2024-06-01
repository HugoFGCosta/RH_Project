
<div class="container mt-5">
    <h1>vacations List</h1>
   <h1>{{22 - $totaldias}} dias de ferias por marcar</h1>

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
                    <td>@if($vacation->vacation_approval_states_id == 3 )
                           <a href="{{url('vacations/'.'edit/' . $vacation->id  )}}"  ><img src="https://as1.ftcdn.net/v2/jpg/00/65/91/40/1000_F_65914012_2seEI4hEtMxEGcU3T64D9y66yM1t9UL2.jpg" height="33px" width="40px" alt=""></a>
                        @elseif($vacation->vacation_approval_states_id == 2 )
                            <img src="https://as2.ftcdn.net/v2/jpg/05/10/34/11/1000_F_510341127_8GUXvIyznz4hekgbCzt0YC0mOoIgo4od.jpg" height="33px" width="40px" alt="">
                        @elseif($vacation->vacation_approval_states_id == 1 )
                            <img src="https://as2.ftcdn.net/v2/jpg/05/19/99/45/1000_F_519994541_TABPKuZ1QFkxo7uo33kYa0CBLnQ5MUq6.jpg" height="33px" width="40px" alt="">
                        @else
                            <img src="" height="33px" width="63px" alt="">
                        @endif</td>

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
