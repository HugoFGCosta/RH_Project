@extends('master.main')

@section('styles')
    <link href="{{ asset('css/dashboard-stats.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-dashboard">
        <div class="custom-row">
            <div class="row1">
                <h1>HORAS DE TRABALHO EXTRAORDINÁRIAS</h1>
                <div class="filter-options">
                    <label for="filter">Escolher:</label>
                    <select id="filter" name="filter">
                        <option value="day">Dia</option>
                        <option value="week" selected>Semana</option>
                        <option value="month">Mês</option>
                    </select>
                    <input type="date" id="startDatePicker" name="start_date" style="display:none;">
                    <input type="date" id="endDatePicker" name="end_date" style="display:none;">
                </div>
            </div>
        </div>
        <div class="row2">
            <div class="col1">
                <div class="table-container">
                    <table class="custom-table" id="extraHoursTable">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horas Extra</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($daysOfWeek as $index => $day)
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ $weeklyHours[$index] ?? 0 }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col2">
                <canvas id="extraHoursChart" class="custom-canvas"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('extraHoursChart').getContext('2d');
            const filterSelect = document.getElementById('filter');
            const startDatePicker = document.getElementById('startDatePicker');
            const endDatePicker = document.getElementById('endDatePicker');
            let chart = createChart(ctx, @json(array_values($daysOfWeek)), @json(array_values($weeklyHours)).map(convertHoursMinutesToDecimal));

            filterSelect.addEventListener('change', function() {
                const filterValue = this.value;
                if (filterValue === 'day') {
                    startDatePicker.style.display = 'inline-block';
                    endDatePicker.style.display = 'inline-block';
                } else {
                    startDatePicker.style.display = 'none';
                    endDatePicker.style.display = 'none';
                    updateTableAndChart(filterValue);
                }
            });

            startDatePicker.addEventListener('change', function() {
                const startDateValue = startDatePicker.value;
                const endDateValue = endDatePicker.value;
                if (startDateValue && endDateValue) {
                    updateTableAndChart('day', startDateValue, endDateValue);
                }
            });

            endDatePicker.addEventListener('change', function() {
                const startDateValue = startDatePicker.value;
                const endDateValue = endDatePicker.value;
                if (startDateValue && endDateValue) {
                    updateTableAndChart('day', startDateValue, endDateValue);
                }
            });

            function createChart(ctx, labels, data) {
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Horas Extra',
                            data: data,
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return convertDecimalToHoursMinutes(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += convertDecimalToHoursMinutes(context.raw);
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function updateTableAndChart(filter, startDate = null, endDate = null) {
                fetch('{{ route("dashboard.filter") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ filter: filter, start_date: startDate, end_date: endDate })
                })
                    .then(response => response.json())
                    .then(data => {
                        updateTable(data.labels, data.data);
                        chart.destroy();
                        chart = createChart(ctx, data.labels, data.data.map(convertHoursMinutesToDecimal));
                    });
            }

            function updateTable(labels, data) {
                const tableBody = document.querySelector('#extraHoursTable tbody');
                tableBody.innerHTML = '';

                labels.forEach((label, index) => {
                    const row = document.createElement('tr');
                    const cell1 = document.createElement('td');
                    cell1.textContent = label;
                    const cell2 = document.createElement('td');
                    cell2.textContent = data[index];
                    row.appendChild(cell1);
                    row.appendChild(cell2);
                    tableBody.appendChild(row);
                });
            }

            function convertDecimalToHoursMinutes(decimalHours) {
                const hours = Math.floor(decimalHours);
                const minutes = Math.round((decimalHours - hours) * 60);
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            }

            function convertHoursMinutesToDecimal(time) {
                const [hours, minutes] = time.split(':').map(Number);
                return hours + (minutes / 60);
            }
        });
    </script>
@endsection
