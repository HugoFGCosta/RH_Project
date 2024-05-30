
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
        <label for="vacation_approval_states_id">Aprovação de ferias</label>
        <select name="vacation_approval_states_id" id="vacation_approval_states_id">
        <option value="1">Aprovar</option>
        <option value="2">Rejeitar</option>
        <option value="3">Pendente</option>
        </select>
        <br>

        <input
            value="{{ $vacations->date_start}}"
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
        <input
       value="{{$vacations->date_end }}"
       @if($totaldias>=22)
           disabled
       @else
           required
       @endif
       type="date"
       name="date_start"
       id="date_start"
        >


    </div>
    <button type="submit" class="btn btn-primary">
        Enviar
    </button>
</form>


