{{-- @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ url('user/presence/store') }}">
    @csrf

    <input type="hidden" id="first_start" name="first_start">
    <input type="hidden" id="first_end" name="first_end">
    <input type="hidden" id="second_start" name="second_start">
    <input type="hidden" id="second_end" name="second_end">

    <div class="form-group">
        <button type="submit" id="entryExitButton" class="btn-in-out">Entrada</button>
    </div>
</form> --}}


{{-- TEMPORARIO ROTA SIMULATED --}}

{{-- @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ url('user/presence/storeSimulated') }}">
    @csrf
    <div>
        <label for="first_start">Início do Primeiro Turno:</label>
        <input type="time" id="first_start" name="first_start" required>
    </div>
    <div>
        <label for="first_end">Fim do Primeiro Turno:</label>
        <input type="time" id="first_end" name="first_end" required>
    </div>
    <div>
        <label for="second_start">Início do Segundo Turno:</label>
        <input type="time" id="second_start" name="second_start" required>
    </div>
    <div>
        <label for="second_end">Fim do Segundo Turno:</label>
        <input type="time" id="second_end" name="second_end" required>
    </div>
    <div>
        <button type="submit">Simular</button>
    </div>
</form> --}}
<form method="POST" action="{{ url('store-presence') }}">
    @csrf
    <input type="hidden" id="first_start" name="first_start" value="{{ $presence->first_start ?? '' }}">
    <input type="hidden" id="first_end" name="first_end" value="{{ $presence->first_end ?? '' }}">
    <input type="hidden" id="second_start" name="second_start" value="{{ $presence->second_start ?? '' }}">
    <input type="hidden" id="second_end" name="second_end" value="{{ $presence->second_end ?? '' }}">
    <button id="entryExitButton" type="submit" class="btn-in" data-shift="{{ isset($presence->second_start) ? 'second' : 'first' }}">
        {{ isset($presence->first_start) ? 'Saída' : 'Entrada' }}
    </button>
</form>
