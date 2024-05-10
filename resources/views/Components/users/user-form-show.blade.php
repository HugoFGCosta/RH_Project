{{-- NAO ESTA PRONTO --}}

<form method="SHOW" action="{{ url('users') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" autocomplete="name" placeholder="Type your name"
            class="form-control" value="{{ isset($user->name) ? $user->name : '' }}" required
            aria-describedby="nameHelp" disabled>
        <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
    </div>
    <a href="/menu" type="button" class="mt-2 mb-5 btn btn-primary">Back</a>
</form>
