

<form method="POST" action="{{ url('vacations') }}">

    @csrf
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="form-group">
        <h1>Criar férias</h1>
        <label for="date_start">start</label>
        <input
            @if($totaldias>=22)
           disabled
            @else
            required
            @endif
            type="date"
            name="date_start"
            id="date_start"

        >
        <label for="date_end">end</label>
        <input   @if($totaldias>=22)
                     disabled
                 @else
                     required
                 @endif
            type="date"
            name="date_end"
            id="date_end"

        >



    </div>
    @error('date_start')
    <p>{{$message}}</p>
    @enderror
    <button  type="submit" class="btn btn-primary">
    Enviar
    </button>
</form>


