{{-- NAO ESTA PRONTO --}}

<form method="SHOW" action="{{ url('users') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" autocomplete="name" placeholder="Type your name"
            class="form-control" value="{{ isset($user->name) ? $user->name : '' }}" required
            aria-describedby="nameHelp" disabled>
    </div>
    <a href="/menu" type="button" class="form-button">Editar</a>
</form>
