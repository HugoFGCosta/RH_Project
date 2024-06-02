

<form method="POST" action="{{ url('vacations') }}">

    @csrf
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}

    </div>
    <div class="form-group">
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


