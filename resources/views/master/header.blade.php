<!-- Barra Lateral -->
<div class="container custom">
    <div class="menu">
        <ul>
            <li id="menu-logo">
                <a href="#">
                    <span class="icon">
                        <ion-icon name="accessibility-outline"></ion-icon>
                    </span>
                    <span class="title">Recursos Humanos</span>
                </a>
            </li>
                <li id="home">
                    <a href="/menu">
                    <span class="icon">
                        <ion-icon name="home-outline"></ion-icon>
                    </span>
                        <span class="title">Home</span>
                    </a>
                </li>

            <li id="daily-tasks">
                <div class="dropdown-main">
                    <a>
                        <span class="icon"><ion-icon name="list-outline"></ion-icon></span>
                        <span class="title">Tarefas Diárias</span>
                    </a>
                    <img src="{{ asset('images/dropdown-arrow.svg') }}" alt="arrow" id="dropdown-arrow">
                </div>
            </li>

            <div id="daily-tasks-content">
                    <li><a href="/register-schedule">Registo de Horário</a></li>
                    <li><a href="/dashboard-statistics">Dashboard Estatísticas</a></li>
                    <li><a href="/attendance-record">Registo de Assiduidade</a></li>
                    <li><a href="/export/work-shifts/{{Auth::user()->id}}">Exportar Horário</a></li>
            </div>

            <li id="requests">
                <div class="dropdown-main">
                    <a href="/requests">
                        <span class="icon"><ion-icon name="arrow-redo-outline"></ion-icon></span>
                        <span class="title">Pedidos</span>
                    </a>
                    <img src="{{ asset('images/dropdown-arrow.svg') }}" alt="arrow" id="dropdown-arrow">
                </div>
            </li>
            <div id="requests-content">
                <li><a href="/users/{{Auth::user()->id}}/absences">Ver faltas</a></li>
                <li><a href="//users/{{Auth::user()->id}}/edit">Gerir dados</a></li>
                <li><a href="{{ url('user/edit') }}">Gerir Dados</a></li>
                <li><a href="{{ url('user/show') }}">Mostrar Dados</a></li>
                <li><a href="/vacation-plans">Plano de férias</a></li>
                <li><a href="/approve-absence">Aprovar Faltas</a></li>
            </div>

            <li id="settings">
                <a href="/settings">
                    <span class="icon"><ion-icon name="settings-outline"></ion-icon></span>
                    <span class="title">Definições</span>
                </a>
            </li>

            <li id="logout">
                <a href="/logout">
                    <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                    <span class="title">Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</div>
