
<form method="POST" action="{{ url('vacations') }}/{{$vacations->id}}">
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
    <h1>Editar férias</h1>
        @method('PUT')
        @if($role >= 3 || $role > $role_id_table)
        <label for="id">id</label>
        <input
            disabled
            value="{{ $vacations->id}}"
            type="text"
            name="id"
            id="id"

        ><br>

        <label for="vacation_approval_states_id">Aprovação de férias</label>
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


