@php
    use Carbon\Carbon;
@endphp
<div class="showform-restricted-container">
    <div class="showform-form-row">
        <div class="showform-input-data full-width">
            <label for="name">Nome</label>
            <p id="name">{{ $user->name }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data full-width">
            <label for="address">Endereço</label>
            <p id="address">{{ $user->address }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="email">Email</label>
            <p id="email">{{ $user->email }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="role">Função</label>
            <p id="role">
                @if($user->role->role == 'Worker')
                    Utilizador
                @elseif($user->role->role == 'Manager')
                    Gestor
                @elseif($user->role->role == 'Administrator')
                    Administrador
                @endif
            </p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="nif">NIF</label>
            <p id="nif">{{ $user->nif }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="tel">Telefone</label>
            <p id="tel">{{ $user->tel }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="birth_date">Data de Nascimento</label>
            <p id="birth_date">{{ Carbon::parse($user->birth_date)->format('d/m/Y') }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="work_schedule">Horário de Trabalho</label>
            @if ($user_shift)
                <p id="user_shift">
                    {{ 'Das ' . Carbon::parse($user_shift->work_shift->start_hour)->format('H:i') . ' às ' . Carbon::parse($user_shift->work_shift->end_hour)->format('H:i') }}
                </p>
            @else
                <p>O usuário não tem um turno de trabalho atribuído.</p>
            @endif
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <a href="/user/edit" class="btn showform-btn"><span>Editar Dados</span></a>
    </div>
</div>
