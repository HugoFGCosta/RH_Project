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
        <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
    </div>
    <button type="submit" class="mt-2 mb-5 btn btn-primary">Save</button>
    <a href="/menu" class="mt-2 mb-5 btn btn-primary">Back</a>
</form>
