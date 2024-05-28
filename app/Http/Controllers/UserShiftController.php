<?php

namespace App\Http\Controllers;

use App\Models\User_shift;
use App\Http\Requests\StoreUser_shiftRequest;
use App\Http\Requests\UpdateUser_shiftRequest;

class UserShiftController extends Controller
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
    public function store(StoreUser_shiftRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User_shift $user_shift)
    {
        $users_shifts = User_shift::all();

        return view('pages.user-shifts.show-all', ['users_shifts' => $users_shifts]);
    }
    public function show_spec(User_shift $user_shift)
    {

        $user = auth()->user();
        $user_shifts = User_Shift::all()->where('user_id', $user->id);

        return view('pages.user-shifts.show-spec', ['user_shifts' => $user_shifts]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User_shift $user_shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUser_shiftRequest $request, User_shift $user_shift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User_shift $user_shift)
    {
        //
    }
}