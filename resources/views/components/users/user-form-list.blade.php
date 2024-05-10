{{-- NAO ESTA PRONTO --}}

{{-- 
<div class="container mt-5">
    <h1>Players List</h1>

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
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Address</th>
                <th scope="col">Description</th>
                <th scope="col">Retired</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
             @forelse($players as $player)
                <tr>
                    <th scope="row"> {{ $player->id }} </th>
                    <td>
                        @if ($player->image)
                          <img class="w-100 img-responsive" src="{{ asset('storage/'.$player->image) }}" alt="" title=""></a>
                        @else
                            <p>
                                No Image
                            </p>
                        @endif
                    </td>
                    <td>{{ $player->name }}</td>
                    <td>{{ $player->address->address}}</td>                      
                    <td>{{ $player->description}}</td>
                    <td>{{ $player->retired }}</td>
                    <td class="d-flex align-items-center">
                        <a href="{{url('players/' . $player->id)}}" class="btn btn-success">Show</a>
                        @auth
                        <a href="{{url('players/' . $player->id . '/edit')}}" type="button" class="btn btn-primary">Edit</a>
                        <form  action="{{url('players/' . $player->id)}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>                        
                        </form>
                        @endauth
                    </td>
                </tr>
            @endforeach
        </tbody>
           <div class="d-flex justify-content-center">
               {{ $players->links() }}
           </div>
    </table>
    <div class="d-flex justify-content-center">
        {{ $players->links() }}
    </div>
</div>
 
 --}}
