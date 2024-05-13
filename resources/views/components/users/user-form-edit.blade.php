{{-- NAO ESTA PRONTO O FRONT END, FALTA INFORMAÇOES A SER MODIFICADAS E SUBMIT  --}}
{{-- para funcionar o resto das informaçoes deve-se: apagar linha 82 e desomentar o bloco #83 ~ #93 no UserController @UPDATE --}}


<form method="POST" action="{{ url('user/edit') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" autocomplete="name" placeholder="Type your name"
            class="form-control" value="{{ isset($user->name) ? $user->name : '' }}" required
            aria-describedby="nameHelp">
        <small id="nameHelp" class="form-text text-muted">Altere o seu nome</small>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" autocomplete="email" placeholder="Type your email"
               class="form-control" value="{{ isset($user->email) ? $user->email : '' }}" required
               aria-describedby="nameHelp">
        <small id="nameHelp" class="form-text text-muted">Altere o seu email</small>
    </div>
    <div class="form-group">
        <label for="email">Password</label>
        <input type="password" id="password" name="password" autocomplete="password" placeholder="Type your password"
               class="form-control" value="{{ isset($user->password) ? $user->password : '' }}" required
               aria-describedby="nameHelp">
        <small id="nameHelp" class="form-text text-muted">Altere a sua password</small>
    </div>
    <button type="submit" class="mt-2 mb-5 btn btn-primary">Save</button>
    <a href="/menu" class="mt-2 mb-5 btn btn-primary">Back</a>
</form>
