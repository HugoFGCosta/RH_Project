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
            <div id="daily-tasks-content" class="dropdown-content">
                <li><a href="/dashboard-statistics">Dashboard Estatísticas</a></li>
                <li><a href="/attendance-record">Registo de Assiduidade</a></li>
                <li><a href="/users/{{Auth::user()->id}}/absences">Ver faltas</a></li>
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
            <div id="requests-content" class="dropdown-content">
                <li><a href="/users/{{Auth::user()->id}}/absences">Ver faltas</a></li>
                <li><a href="/vacation">Plano de férias</a></li>
            </div>

            @if(in_array(Auth::user()->role_id, [2, 3]))
                <li id="settings">
                    <div class="dropdown-main"><a href="/settings">
                            <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                            <span class="title">Gestão</span>
                        </a>
                        <img src="{{ asset('images/dropdown-arrow.svg') }}" alt="arrow" id="dropdown-arrow">
                    </div>
                </li>
                <div id="requests-content" class="dropdown-content">
                    @if(in_array(Auth::user()->role_id, [3]))
                        <li><a href="/import-export-data">Importar/Exportar Dados</a></li>
                        <li><a href="/admin-register">Criar Funcionário</a></li>
                    @endif
                    <li><a href="/work-shifts">Horários</a></li>
                    <li><a href="/users/show-all">Listar Funcionários</a></li>
                    <li><a href="/pending-justifications">Justificações</a></li>
                    <li><a href="/absences">Gestão de Faltas</a></li>
                    <li><a href="/work-times">Períodos de trabalho</a></li>
                </div>
            @endif

            <li id="logout">
                <a href="/logout">
                    <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                    <span class="title">Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</div>
