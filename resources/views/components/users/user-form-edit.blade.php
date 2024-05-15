<form method="POST" action="{{ url('user/edit') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-row">
        <div class="input-data">
            <input type="text" id="name" name="name" autocomplete="name" value="{{ isset($user->name) ? $user->name : '' }}" required>
            <div class="underline"></div>
            <label for="name">Name</label>
        </div>
    </div>
    <div class="form-row">
        <div class="input-data">
            <input type="email" id="email" name="email" autocomplete="email" value="{{ isset($user->email) ? $user->email : '' }}" required>
            <div class="underline"></div>
            <label for="email">Email</label>
        </div>
    </div>
    <div class="form-row">
        <div class="input-data">
            <input type="password" id="password" name="password" autocomplete="new-password" required>
            <div class="underline"></div>
            <label for="password">Password</label>
        </div>
    </div>
    <div class="form-row submit-btn">
        <div class="input-data">
            <div class="inner"></div>
            <input type="submit" value="Save">
        </div>
    </div>
    <div class="form-row">
        <a href="/menu" class="btn btn-primary">Back</a>
    </div>
</form>
