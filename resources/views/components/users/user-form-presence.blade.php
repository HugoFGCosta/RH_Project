{{-- METODO PARA TESTAR O STORE --}}




{{-- @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ url('user/presence/store') }}">


    @method('POST')
    @csrf

    <h1>Registro MANUAL - user/presence/store </h1>
    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Register') }}
            </button>
        </div>
    </div>
    <br>
</form>

<hr />
<hr />
<hr />
<hr />
<hr /> --}}









{{-- METODO PARA TESTAR O STORESIMULATED --}}


@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


<form method="POST" action="{{ url('user/presence/storeSimulated') }}">

    @method('POST')
    @csrf

    <label for="first_start">Entrar:</label><br>
    <input type="text" id="first_start" name="first_start"><br>
    <label for="first_end">Sair:</label><br>
    <input type="text" id="first_end" name="first_end"><br>
    <label for="second_start">Voltar:</label><br>
    <input type="text" id="second_start" name="second_start"><br>
    <label for="second_end">Sair:</label><br>
    <input type="text" id="second_end" name="second_end">
    <br>
    <br>
    <br>

    <button type="submit" id="simulate">Simular</button>
</form>

<script>
    document.getElementById('simulate').addEventListener('click', function(e) {
        e.preventDefault();

        // Crie objetos Date com as datas e horas desejadas
        var first_start = new Date('2024-05-12T10:00:00');
        var first_end = new Date('2024-05-12T12:00:00');
        var second_start = new Date('2024-05-12T13:00:00');
        var second_end = new Date('2024-05-12T19:00:00');

        // Converta os objetos Date para strings no formato 24 horas
        document.getElementById('first_start').value = first_start.toLocaleString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        document.getElementById('first_end').value = first_end.toLocaleString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        document.getElementById('second_start').value = second_start.toLocaleString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        document.getElementById('second_end').value = second_end.toLocaleString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        // Submeta o formulário
        e.target.form.submit();
    });
</script>
