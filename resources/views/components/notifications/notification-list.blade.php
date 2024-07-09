<div class="container mt-5">
    <h1>Notificaçoes de hoje</h1>

    <form id="notifications-form" action="{{ route('notifications.changeState') }}" method="POST">
        @csrf
        <table class="table-notifications">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">TIPO</th>
                    <th scope="col">DESCRIÇÃO</th>
                    <th scope="col">STATE</th>
                    <th scope="col">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                    @if ($notification->state == 0)
                        <tr>
                            {{-- TD # --}}
                            <td>
                                <input type="checkbox" name="notifications[{{ $notification->id }}][id]"
                                    value="{{ $notification->id }}">
                                <input type="hidden" name="notifications[{{ $notification->id }}][events_id]"
                                    value="{{ $notification->events_id }}">
                                <input type="hidden" name="notifications[{{ $notification->id }}][absence_id]"
                                    value="{{ $notification->absence_id }}">
                                <input type="hidden" name="notifications[{{ $notification->id }}][vacation_id]"
                                    value="{{ $notification->vacation_id }}">
                                <input type="hidden" name="notifications[{{ $notification->id }}][state]"
                                    value="{{ $notification->state }}">
                            </td>

                            {{-- TD TIPO --}}
                            @if ($notification->events_id != null)
                                <td>EVENTOS</td>
                            @elseif ($notification->absence_id != null)
                                <td>FALTAS</td>
                            @elseif ($notification->vacation_id != null)
                                <td>FERIAS</td>
                            @endif

                            {{--  TD DESCRIÇÃO --}}
                            @if ($notification->events_id != null)
                                <td>{{ $notification->event->title }}</td>
                            @elseif ($notification->absence_id != null)
                                @if ($notification->absence->absence_states_id == 1)
                                    <td>Aprovado</td>
                                @elseif($notification->absence->absence_states_id == 2)
                                    <td>Rejeitado</td>
                                @elseif($notification->absence->absence_states_id == 3)
                                    <td>Pendente</td>
                                @else
                                    <td>Injustificado</td>
                                @endif
                            @elseif ($notification->vacation_id != null)
                                @if ($notification->vacation->vacation_approval_states_id == 1)
                                    <td> Aprovado</td>
                                @elseif ($notification->vacation->vacation_approval_states_id == 2)
                                    <td> Negado</td>
                                @elseif ($notification->vacation->vacation_approval_states_id == 3)
                                    <td> Pendente</td>
                                @endif
                            @endif

                            {{-- TD STATE --}}
                            @if ($notification->state == 0)
                                <td>Nao Lido</td>
                            @else
                                <td>Lido</td>
                            @endif

                            {{-- TD AÇÃO --}}
                            @if ($notification->events_id != null)
                                <td> --- </td>
                            @elseif ($notification->absence_id != null)
                                <td><a href="/pending-justifications"> Justificar</a></td>
                            @elseif ($notification->vacation_id != null)
                                <td> --- </td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <button type="submit">MARCAR LIDO</button>
    </form>
</div>
