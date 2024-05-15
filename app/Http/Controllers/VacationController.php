<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;

class VacationController extends Controller
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
    public function store(StoreVacationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacation $vacation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacation $vacation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        //
    }

    public function exportCSVVacations() //exporta os dados dos utilizadores para um ficheiro CSV
    {
        $filename = 'vacation-data.csv';

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
                'vacation_approval_states_id',
                'approved_by',
                'date_start',
                'date_end',
                'created_at',
                'updated_at',

            ]);

            // Fetch and process data in chunks
            Vacation::chunk(25, function ($vacations) use ($handle) {
                foreach ($vacations as $vacation) {
                    // Extract data from each employee.
                    $data = [
                        isset($vacation->id)? $vacation->id : '',
                        isset($vacation->user_id)? $vacation->user_id : '',
                        isset($vacation->vacation_approval_states_id)? $vacation->vacation_approval_states_id : '',
                        isset($vacation->approved_by)? $vacation->approved_by : '',
                        isset($vacation->date_start)? $vacation->date_start : '',
                        isset($vacation->date_end)? $vacation->date_end : '',
                        isset($vacation->created_at)? $vacation->created_at : '',
                        isset($vacation->updated_at)? $vacation->updated_at : '',
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
