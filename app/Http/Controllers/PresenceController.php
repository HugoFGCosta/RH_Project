<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;

class PresenceController extends Controller
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
    public function store(StorePresenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence)
    {
        //
    }

    public function exportCSVPresences() //exporta os dados dos utilizadores para um ficheiro CSV
    {
        $filename = 'presence-data.csv';

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
                'first_start',
                'first_end',
                'second_start',
                'second_end',
                'extra_hour',
                'effective_hour',
                'created_at',
                'updated_at',
            ]);

            // Fetch and process data in chunks
            Presence::chunk(25, function ($presences) use ($handle) {
                foreach ($presences as $presence) {
                    // Extract data from each employee.
                    $data = [
                        isset($presence->id)? $presence->id : '',
                        isset($presence->user_id)? $presence->user_id : '',
                        isset($presence->first_start)? $presence->first_start : '',
                        isset($presence->first_end)? $presence->first_end : '',
                        isset($presence->second_start)? $presence->second_start : '',
                        isset($presence->second_end)? $presence->second_end : '',
                        isset($presence->extra_hour)? $presence->extra_hour : '',
                        isset($presence->effective_hour)? $presence->effective_hour : '',
                        isset($presence->created_at)? $presence->created_at : '',
                        isset($presence->updated_at)? $presence->updated_at : '',

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
