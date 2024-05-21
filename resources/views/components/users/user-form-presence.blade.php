@if (session('error'))
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
</form>
