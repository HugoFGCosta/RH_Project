
<form method="POST" action="{{ url('vacations') }}/{{$vacations->id}}">
    @csrf
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}

    </div>
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
        @if($role > 1)

        <label for="vacation_approval_states_id">Aprovação de ferias</label>
        <select name="vacation_approval_states_id" id="vacation_approval_states_id">
        <option value="3">Pendente</option>
        <option value="1">Aprovar</option>
        <option value="2">Rejeitar</option>-

        </select>
        <br>



        @endif
        <input
            value="{{ $vacations->date_start}}"


            required
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


