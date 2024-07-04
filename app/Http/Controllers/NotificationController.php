<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::with(['event', 'absence', 'vacation'])
            ->where('state', 0)
            ->where('user_id', $user->id)
            ->get();
        return response()->json(['notifications' => $notifications]);
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
    public function store(StoreNotificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        $user = auth()->user();
        $notifications = Notification::with(['event', 'absence', 'vacation'])
            ->where('state', 0)
            ->where('user_id', $user->id)
            ->get();

        if (request()->ajax()) {
            return response()->json($notifications);
        }

        return view('pages.notifications.show', ['notifications' => $notifications]);
    }

    public function showNotifications()
    {
        $user = auth()->user();
        $notifications = Notification::with('event', 'absence', 'vacation')
            ->where('state', 0)
            ->where('user_id', $user->id)
            ->get();

        return view('pages.notifications.show', compact('notifications'));
    }

    public function changeState(Request $request)
    {
        $notifications = $request->input('notifications', []);

        foreach ($notifications as $notificationData) {
            if (isset($notificationData['id'])) {
                \DB::table('notifications')
                    ->where('id', $notificationData['id'])
                    ->update(['state' => 1]);
            }
        }

        return redirect()->route('menu')->with('status', 'Notificações marcadas como lidas.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}