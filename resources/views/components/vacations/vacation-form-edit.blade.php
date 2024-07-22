<form method="POST" action="{{ url('vacations') }}/{{$vacations->id}}" class="vacation-form">
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
        @method('PUT')
        @if($role >= 3 || $role > $role_id_table)
            <div class="input-group">
                <label for="id">ID</label>
                <input
                    disabled
                    value="{{ $vacations->id }}"
                    type="text"
                    name="id"
                    id="id"
                >
            </div>
            <div class="input-group">
                <label for="vacation_approval_states_id">Aprovação de Férias</label>
                <select @if($vacations->vacation_approval_states_id == 2) disabled @endif name="vacation_approval_states_id" id="vacation_approval_states_id">
                    <option value="3">Pendente</option>
                    <option value="1">Aprovar</option>
                    <option value="2">Rejeitar</option>
                </select>
            </div>
        @endif

        <div class="input-group">
            <label for="date_start">Início</label>
            <input
                @if($vacations->vacation_approval_states_id == 2) disabled @endif
                value="{{ $vacations->date_start }}"
                required
                type="date"
                name="date_start"
                id="date_start"
            >
        </div>

        <div class="input-group">
            <label for="date_end">Fim</label>
            <input
                @if($vacations->vacation_approval_states_id == 2) disabled @endif
                value="{{ $vacations->date_end }}"
                required
                type="date"
                name="date_end"
                id="date_end"
            >
        </div>
    </div>

    @error('date_start')
    <p class="error-message"><i class="ion-icon ion-alert-circled"></i>{{ $message }}</p>
    @enderror

    <button @if($vacations->vacation_approval_states_id == 2) disabled @endif type="submit" class="btn btn-primary">Enviar</button>
</form>
