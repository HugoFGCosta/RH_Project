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




    @if ($user_shift)
        <div class="form-group">
            <label for="workShiftId">ID do Turno de Trabalho:</label>
            <input type="text" class="form-control" id="workShiftId"
                value="{{ $user_shift->work_shift->start_hour . ' ~ ' . $user_shift->work_shift->end_hour }}" readonly>
        </div>
    @else
        <p>O usuário não tem um turno de trabalho atribuído.</p>
    @endif


    <a href="/menu" type="button" class="mt-2 mb-5 btn btn-primary">Back</a>
</form>
