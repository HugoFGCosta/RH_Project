
<div class="showform-form">
    <div class="showform-restricted-container">
        <div class="showform-form-row">
            <div class="showform-input-data">
                <label for="name">Nome:</label>
                <p id="name">{{ $user->name }}</p>
                <div class="showform-underline"></div>
            </div>
        </div>
        <div class="showform-form-row">
            <div class="showform-input-data">
                <label for="email">Email:</label>
                <p id="email">{{ $user->email }}</p>
                <div class="showform-underline"></div>
            </div>
        </div>
        <div class="showform-form-row">
            <div class="showform-input-data">
                <label for="role">Role:</label>
                {{--<p id="role">{{ $user->role->role }}</p>--}}
                <div class="showform-underline"></div>
            </div>
        </div>
        <div class="showform-form-row">
            <div class="showform-input-data">
                <label for="address">Endereço:</label>
                <p id="address">{{ $user->address }}</p>
                <div class="showform-underline"></div>
            </div>
        </div>
        <div class="showform-form-row">
            <form action="/user/edit" method="get">
                <button type="submit" class="btn btn-primary showform-btn">Editar Dados</button>
            </form>
        </div>
    </div>
</div>
