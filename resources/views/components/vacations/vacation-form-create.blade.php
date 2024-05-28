<form method="POST" action="{{ url('vacations') }}">
    @csrf
    <div class="form-group">

        <label for="date_start">start</label>
        <input
            required
            type="date"
            name="date_start"
            id="date_start"

        >
        <label for="date_end">end</label>
        <input
            required
            type="date"
            name="date_end"
            id="date_end"

        >


    </div>
    @error('date_start')
    <p>{{$message}}</p>
    @enderror
    <button type="submit" class="btn btn-primary">
    Enviar
    </button>
</form>


