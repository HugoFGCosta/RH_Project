<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Função para converter decimal para horas e minutos
    private function convertDecimalToHoursMinutes($decimalHours)
    {
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function statistics()
    {
        $user = Auth::user();

        $presences = $user->presences()->get()->map(function($entry) {
            $entry->dayOfWeek = Carbon::parse($entry->created_at)->dayOfWeekIso;
            $entry->hours_numeric = $entry->extra_hour;
            return $entry;
        });

        // Lista de todos os dias da semana
        $daysOfWeek = [
            1 => 'Segunda-Feira',
            2 => 'Terça-Feira',
            3 => 'Quarta-Feira',
            4 => 'Quinta-Feira',
            5 => 'Sexta-Feira',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        // Inicializar horas extras para todos os dias da semana com 0
        $hoursExtraByDay = array_fill(1, 7, 0);

        // Preencher horas extras reais
        foreach ($presences as $presence) {
            $hoursExtraByDay[$presence->dayOfWeek] += $presence->hours_numeric;
        }
        // Converte horas extras para horas e minutos reais
        foreach ($hoursExtraByDay as $day => $hours) {
            $hoursExtraByDay[$day] = $this->convertDecimalToHoursMinutes($hours);
        }

        // Lógica para mostrar a semana atual
        $weeklyPresences = $user->presences()->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get()->groupBy(function($date) {
            return Carbon::parse($date->created_at)->dayOfWeekIso;
        });

        $weeklyHours = array_fill(1, 7, 0);
        foreach ($weeklyPresences as $dayOfWeek => $entries) {
            $weeklyHours[$dayOfWeek] = $entries->sum('extra_hour');
        }

        // Converte horas semanais para horas e minutos reais
        foreach ($weeklyHours as $day => $hours) {
            $weeklyHours[$day] = $this->convertDecimalToHoursMinutes($hours);
        }

        return view('pages.dashboard-stats.dashboard-stats', compact('hoursExtraByDay', 'daysOfWeek', 'weeklyHours'));
    }

    public function filterStatistics(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $labels = [];
        $data = [];

        if ($filter === 'day' && $startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay(); // Incluir o final do dia

            // Presenças no intervalo de datas e agrupe por data
            $presences = $user->presences()->whereBetween('created_at', [$startDate, $endDate])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });

            // Labels e data arrays
            foreach ($presences as $date => $entries) {
                $labels[] = $date;
                $data[] = $this->convertDecimalToHoursMinutes($entries->sum('extra_hour'));
            }
        } else {
            switch ($filter) {
                case 'day':
                    $presences = $user->presences()->get()->map(function ($entry) {
                        $entry->dayOfWeek = Carbon::parse($entry->created_at)->dayOfWeekIso;
                        $entry->hours_numeric = $entry->extra_hour;
                        return $entry;
                    });

                    $daysOfWeek = [
                        1 => 'Segunda-Feira',
                        2 => 'Terça-Feira',
                        3 => 'Quarta-Feira',
                        4 => 'Quinta-Feira',
                        5 => 'Sexta-Feira',
                        6 => 'Sábado',
                        7 => 'Domingo',
                    ];

                    $labels = array_values($daysOfWeek);
                    $data = array_fill(1, 7, 0);

                    foreach ($presences as $presence) {
                        $data[$presence->dayOfWeek] += $presence->hours_numeric;
                    }

                    // Converte horas diárias para horas e minutos reais
                    foreach ($data as $day => $hours) {
                        $data[$day] = $this->convertDecimalToHoursMinutes($hours);
                    }
                    break;

                case 'week':
                    $presences = $user->presences()->whereBetween('created_at', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear()
                    ])->get()->map(function ($entry) {
                        $entry->weekOfYear = Carbon::parse($entry->created_at)->weekOfYear;
                        $entry->hours_numeric = $entry->extra_hour;
                        return $entry;
                    });

                    $labels = [];
                    $data = [];

                    for ($week = 1; $week <= 52; $week++) {
                        $labels[] = "Semana $week";
                        $data[] = $this->convertDecimalToHoursMinutes($presences->where('weekOfYear', $week)->sum('hours_numeric'));
                    }
                    break;

                case 'month':
                    $presences = $user->presences()->whereBetween('created_at', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear()
                    ])->get()->map(function ($entry) {
                        $entry->month = Carbon::parse($entry->created_at)->month;
                        $entry->hours_numeric = $entry->extra_hour;
                        return $entry;
                    });

                    $months = [
                        1 => 'Janeiro',
                        2 => 'Fevereiro',
                        3 => 'Março',
                        4 => 'Abril',
                        5 => 'Maio',
                        6 => 'Junho',
                        7 => 'Julho',
                        8 => 'Agosto',
                        9 => 'Setembro',
                        10 => 'Outubro',
                        11 => 'Novembro',
                        12 => 'Dezembro',
                    ];

                    $labels = array_values($months);
                    $data = array_fill(0, 12, 0);

                    foreach ($presences as $presence) {
                        $data[$presence->month - 1] += $presence->hours_numeric;
                    }

                    // Converte horas mensais para horas e minutos reais
                    foreach ($data as $month => $hours) {
                        $data[$month] = $this->convertDecimalToHoursMinutes($hours);
                    }
                    break;
            }
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
