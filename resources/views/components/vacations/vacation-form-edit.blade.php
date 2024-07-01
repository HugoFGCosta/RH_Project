<form method="POST" action="{{ url('vacations') }}/{{ $vacations->id }}">
    @csrf
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
    </div>
    <div class="form-group">
        @method('PUT')
        <label for="id">ID</label>
        <input disabled value="{{ $vacations->id }}" type="text" name="id" id="id"><br>

        @if ($role > 1)
            <label for="vacation_approval_states_id">Aprovação de férias</label>
            <select name="vacation_approval_states_id" id="vacation_approval_states_id">
                <option value="3" {{ $vacations->vacation_approval_states_id == 3 ? 'selected' : '' }}>Pendente
                </option>
                <option value="1" {{ $vacations->vacation_approval_states_id == 1 ? 'selected' : '' }}>Aprovar
                </option>
                <option value="2" {{ $vacations->vacation_approval_states_id == 2 ? 'selected' : '' }}>Rejeitar
                </option>
            </select>
            <br>
        @endif

        <input value="{{ $vacations->date_start }}" required type="date" name="date_start" id="date_start">
        <label for="date_end">End</label>
        <input value="{{ $vacations->date_end }}" required type="date" name="date_end" id="date_end">
    </div>

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<!-- Script para Pusher -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Configuração do Pusher
    Pusher.logToConsole = true;
    var pusher = new Pusher('f680f8f49f5085f74a06', {
        cluster: 'ap1'
    });

    // Assinando o canal 'notification-channel'
    var channel = pusher.subscribe('notification-channel');
    channel.bind('notification-event', function(data) {
        alert(JSON.stringify(data));
        // Você pode modificar essa parte para exibir a notificação de uma maneira mais amigável ao usuário
        // Por exemplo, usando um alert ou mostrando uma mensagem na tela
    });
</script>
