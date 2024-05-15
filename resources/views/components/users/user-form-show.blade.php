<div class="showform-restricted-container">
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="name">Nome:</label>
            <p id="name">{{ $user->name }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="email">Email:</label>
            <p id="email">{{ $user->email }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="role">Função</label>
            <p id="role">{{ $user->role->role }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="address">Endereço:</label>
            <p id="address">{{ $user->address }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="nif">NIF</label>
            <p id="nif">{{ $user->nif }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="tel">Telefone</label>
            <p id="tel">{{ $user->tel }}</p>
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <div class="showform-input-data">
            <label for="birth_date">Data de Nascimento</label>
            <p id="birth_date">{{ $user->birth_date }}</p>
            <div class="showform-underline"></div>
        </div>
        <div class="showform-input-data">
            <label for="work_schedule">Horário de Trabalho</label>
            {{--<p id="work_schedule">{{'Das' . $user->user_shifts->work_shift->start_hour , 'às'  }}</p>--}}
            <div class="showform-underline"></div>
        </div>
    </div>
    <div class="showform-form-row">
        <form action="/user/edit" method="get">
            <button type="submit" class="btn btn-primary showform-btn">Editar Dados</button>
        </form>
    </div>
</div>
