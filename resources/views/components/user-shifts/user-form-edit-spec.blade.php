<form method="POST" action="{{ route('user_shift.update', $user_shifts) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
        <label>{{ $user_shifts->user->name }}</label>
    </div>
    <div class="input-data">
        <label for="work_shift_id">{{ __('Work Shift') }}</label>
        <select name="work_shift_id">
            @foreach ($work_shifts as $work_shift)
                <option value="{{ $work_shift->id }}"
                    {{ $user_shifts->work_shift_id == $work_shift->id ? 'selected' : '' }}>
                    {{ $work_shift->start_hour . ' ~ ' . $work_shift->break_start . ' - ' . $work_shift->break_end . ' ~ ' . $work_shift->end_hour }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-row">
        <button type="submit" class="btn showform-btn">
            <span>{{ __('Guarde') }}</span>
        </button>
    </div>

</form>
