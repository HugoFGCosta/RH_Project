<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/pt-br.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        .vacation-event {
            background-color: green !important;
            border-color: green !important;
        }

        .absence-event {
            background-color: red !important;
            border-color: red !important;
        }
    </style>
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
            <div class="form-group-event calendar-buttons">
                <button type="button" id="saveEvent">Guardar Evento</button>
                <button type="button" id="deleteEvent" class="btn-delete">Apagar Evento</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var SITEURL = "{{ url('/') }}";
        var currentEvent;
        var absenceDays = {};

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
                            var eventIdPrefix = '';
                            if (this.is_vacation) {
                                eventIdPrefix = 'vacation-';
                            } else if (this.is_absence) {
                                eventIdPrefix = 'absence-';
                            } else {
                                eventIdPrefix = 'event-';
                            }
                            var eventId = eventIdPrefix + this.id;

                            events.push({
                                id: eventId,
                                title: this.title,
                                start: this.start,
                                end: moment(this.end).add(1, 'days').format('YYYY-MM-DD'),
                                allDay: true,
                                className: this.is_vacation ? 'vacation-event' : (this.is_absence ? 'absence-event' : ''),
                                durationEditable: false,
                                editable: !(this.is_vacation || this.is_absence),
                                eventStartEditable: !(this.is_vacation || this.is_absence),
                                eventDurationEditable: !(this.is_vacation || this.is_absence)
                            });

                            if (this.is_absence) {
                                var startDate = moment(this.start).format('YYYY-MM-DD');
                                var endDate = moment(this.end).format('YYYY-MM-DD');
                                absenceDays[startDate] = 'Falta';
                                absenceDays[endDate] = 'Falta';
                            }
                        });
                        callback(events);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao carregar eventos:', xhr.responseText);
                    }
                });
            },
            displayEventTime: false,
            eventRender: function(event, element) {
                if (event.className.includes('vacation-event')) {
                    element.addClass('vacation-event');
                } else if (event.className.includes('absence-event')) {
                    element.addClass('absence-event');
                }
            },
            selectable: true,
            selectHelper: true,
            select: function(start, end) {
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
                                end: moment(endDate).format('YYYY-MM-DD'),
                                type: 'add'
                            },
                            type: "POST",
                            success: function(data) {
                                if (data.message) {
                                    alert(data.message);
                                } else {
                                    displayMessage("Evento criado com sucesso");

                                    calendar.fullCalendar('refetchEvents');

                                    $('#eventModal').css('display', 'none');
                                    calendar.fullCalendar('unselect');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro ao salvar evento:', xhr.responseText);
                            }
                        });
                    }
                });
            },
            eventDrop: function(event, delta, revertFunc) {
                if (event.className.includes('vacation-event') || event.className.includes('absence-event')) {
                    revertFunc();
                    return;
                }
                var start = event.start.format("YYYY-MM-DD");
                var end = (event.end) ? event.end.format("YYYY-MM-DD") : start;

                $.ajax({
                    url: SITEURL + '/fullcalenderAjax',
                    data: {
                        title: event.title,
                        start: start,
                        end: moment(end).subtract(1, 'days').format('YYYY-MM-DD'),
                        id: event.id.replace('event-', ''),
                        type: 'update',
                    },
                    type: "POST",
                    success: function(response) {
                        displayMessage("Evento atualizado com sucesso");
                    },
                    error: function() {
                        console.error('Erro ao atualizar evento');
                        revertFunc();
                    }
                });
            },
            eventClick: function(event) {
                if (event.className.includes('vacation-event') || event.className.includes('absence-event')) {
                    return;
                }
                currentEvent = event;
                $('#eventTitle').val(event.title);
                $('#startDate').val(moment(event.start).format('YYYY-MM-DD'));
                $('#endDate').val(moment(event.end).subtract(1, 'days').format('YYYY-MM-DD'));
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
                                end: moment(endDate).format('YYYY-MM-DD'),
                                id: currentEvent.id.replace('event-', ''),
                                type: 'update'
                            },
                            type: "POST",
                            success: function(response) {
                                displayMessage("Evento atualizado com sucesso");
                                calendar.fullCalendar('refetchEvents');
                                $('#eventModal').css('display', 'none');
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro ao atualizar evento:', xhr.responseText);
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
                                id: currentEvent.id.replace('event-', ''),
                                type: 'delete'
                            },
                            success: function(response) {
                                calendar.fullCalendar('removeEvents', currentEvent.id);
                                displayMessage("Evento excluído com sucesso");
                                $('#eventModal').css('display', 'none');
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro ao excluir evento:', xhr.responseText);
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
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    });
</script>

</body>

</html>
