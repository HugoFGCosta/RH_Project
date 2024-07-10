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
