<!DOCTYPE html>
<html>

<head>
    <title>Laravel FullCalendar Tutorial - ItSolutionStuff.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/pt-br.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>

<div class="container">
    <div id='calendar'></div>
</div>

<!-- Modal -->
<div id="eventModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="eventForm">
            <div class="form-group-event">
                <label for="eventTitle">Título do Evento</label>
                <input type="text" id="eventTitle" required>
            </div>
            <div class="form-group-event">
                <label for="startDate">Data de Início</label>
                <input type="date" id="startDate" required>
            </div>
            <div class="form-group-event">
                <label for="endDate">Data de Fim</label>
                <input type="date" id="endDate" required>
            </div>
            <div class="form-group-event">
                <button type="button" id="saveEvent">Salvar Evento</button>
                <button type="button" id="deleteEvent" class="btn-delete">Apagar Evento</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var SITEURL = "{{ url('/') }}";
        var currentEvent;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var calendar = $('#calendar').fullCalendar({
            locale: 'pt-br',
            editable: true,
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: SITEURL + "/fullcalender",
                    type: 'GET',
                    data: {
                        start: start.format(),
                        end: end.format()
                    },
                    success: function(data) {
                        var events = [];
                        $(data).each(function() {
                            events.push({
                                id: this.id,
                                title: this.title,
                                start: this.start,
                                end: moment(this.end).add(1, 'days').format('YYYY-MM-DD'),
                                allDay: this.allDay
                            });
                        });
                        callback(events);
                    }
                });
            },
            displayEventTime: false,
            eventRender: function(event, element, view) {
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: true,
            selectHelper: true,
            select: function(start, end, allDay) {
                $('#startDate').val(moment(start).format('YYYY-MM-DD'));
                $('#endDate').val(moment(end).subtract(1, 'days').format('YYYY-MM-DD'));
                $('#eventTitle').val('');
                $('#eventModal').css('display', 'block');

                $('#saveEvent').off('click').on('click', function() {
                    var title = $('#eventTitle').val();
                    var startDate = $('#startDate').val();
                    var endDate = $('#endDate').val();

                    if (title) {
                        $.ajax({
                            url: SITEURL + "/fullcalenderAjax",
                            data: {
                                title: title,
                                start: startDate,
                                end: moment(endDate).format('YYYY-MM-DD'), // Adiciona um dia para garantir o fim correto
                                type: 'add'
                            },
                            type: "POST",
                            success: function(data) {
                                displayMessage("Evento criado com sucesso");

                                calendar.fullCalendar('renderEvent', {
                                    id: data.id,
                                    title: title,
                                    start: startDate,
                                    end: moment(endDate).add(1, 'days').format('YYYY-MM-DD'), // Adiciona um dia para garantir o fim correto
                                    allDay: allDay
                                }, true);

                                calendar.fullCalendar('unselect');
                                $('#eventModal').css('display', 'none');
                            }
                        });
                    }
                });
            },
            eventDrop: function(event, delta) {
                var start = event.start.format("YYYY-MM-DD");
                var end = (event.end) ? event.end.format("YYYY-MM-DD") : start;

                $.ajax({
                    url: SITEURL + '/fullcalenderAjax',
                    data: {
                        title: event.title,
                        start: start,
                        end: moment(end).subtract(1, 'days').format('YYYY-MM-DD'),
                        id: event.id,
                        type: 'update'
                    },
                    type: "POST",
                    success: function(response) {
                        displayMessage("Evento atualizado com sucesso");
                        calendar.fullCalendar('updateEvent', event);
                    }
                });
            },
            eventClick: function(event) {
                currentEvent = event;
                $('#eventTitle').val(event.title);
                $('#startDate').val(moment(event.start).format('YYYY-MM-DD'));
                $('#endDate').val(moment(event.end).subtract(1, 'days').format('YYYY-MM-DD')); // Ajusta a data final para exibição correta
                $('#eventModal').css('display', 'block');

                $('#saveEvent').off('click').on('click', function() {
                    var title = $('#eventTitle').val();
                    var startDate = $('#startDate').val();
                    var endDate = $('#endDate').val();

                    if (title) {
                        $.ajax({
                            url: SITEURL + "/fullcalenderAjax",
                            data: {
                                title: title,
                                start: startDate,
                                end: moment(endDate).add(1, 'days').format('YYYY-MM-DD'), // Adiciona um dia para garantir o fim correto
                                id: currentEvent.id,
                                type: 'update'
                            },
                            type: "POST",
                            success: function(response) {
                                displayMessage("Evento atualizado com sucesso");

                                currentEvent.title = title;
                                currentEvent.start = startDate;
                                currentEvent.end = moment(endDate).add(1, 'days').format('YYYY-MM-DD'); // Adiciona um dia para garantir o fim correto
                                calendar.fullCalendar('updateEvent', currentEvent);

                                $('#eventModal').css('display', 'none');
                            }
                        });
                    }
                });

                $('#deleteEvent').off('click').on('click', function() {
                    var deleteMsg = confirm("Deseja realmente excluir?");
                    if (deleteMsg) {
                        $.ajax({
                            type: "POST",
                            url: SITEURL + '/fullcalenderAjax',
                            data: {
                                id: currentEvent.id,
                                type: 'delete'
                            },
                            success: function(response) {
                                calendar.fullCalendar('removeEvents', currentEvent.id);
                                displayMessage("Evento excluído com sucesso");
                                $('#eventModal').css('display', 'none');
                            }
                        });
                    }
                });
            }
        });

        function displayMessage(message) {
            toastr.success(message, 'Evento');
        }

        var modal = document.getElementById("eventModal");
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    });
</script>

</body>

</html>
