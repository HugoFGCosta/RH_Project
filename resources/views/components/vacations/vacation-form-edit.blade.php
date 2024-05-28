
<form method="POST" action="{{url('vacations/' .'edit/' .$vacations->id)}}">
    @csrf
    <div class="form-group">

        @method('PUT')
        <label for="id">id</label>
        <input
            disabled
            value="{{ $vacations->id}}"
            type="text"
            name="id"
            id="id"

        ><br>
        <input
            value="{{ $vacations->date_start}}"
            type="date"
            name="date_start"
            id="date_start"

        >
        <label for="date_end">end</label>
        <input
       value="{{$vacations->date_end }}"
            required
            type="date"
            name="date_end"
            id="date_end"

        >


    </div>
    <button type="submit" class="btn btn-primary">
        Enviar
    </button>
</form>


