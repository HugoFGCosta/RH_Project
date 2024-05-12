<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAbsenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAbsenceRequest $request, Absence $absence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        //
    }

    public function exportCSVAbsences() //exporta os dados dos utilizadores para um ficheiro CSV
    {
        $filename = 'absence-data.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'id',
                'user_id',
                'absence_states_id',
                'approved_by',
                'absence_date',
                'justification',
                'created_at',
                'updated_at',
            ]);

            // Fetch and process data in chunks
            Absence::chunk(25, function ($absences) use ($handle) {
                foreach ($absences as $absence) {
                    // Extract data from each employee.
                    $data = [
                        isset($absence->id)? $absence->id : '',
                        isset($absence->user_id)? $absence->user_id : '',
                        isset($absence->absence_states_id)? $absence->absence_states_id : '',
                        isset($absence->approved_by)? $absence->approved_by : '',
                        isset($absence->absence_date)? $absence->absence_date : '',
                        isset($absence->justification)? $absence->justification : '',
                        isset($absence->created_at)? $absence->created_at : '',
                        isset($absence->updated_at)? $absence->updated_at : '',
                    ];

                    // Write data to a CSV file.
                    fputcsv($handle, $data);
                }
            });

            // Close CSV file handle
            fclose($handle);
        }, 200, $headers);
    }
}
