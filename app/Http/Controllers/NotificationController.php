<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Metodo Index - Mostra todas as notificaçoes do user logado.
    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::with(['event', 'absence', 'vacation'])
            ->where('state', 0)
            ->where('user_id', $user->id)
            ->get();
        return response()->json(['notifications' => $notifications]);
    }


    public function create()
    {
        //
    }


    public function store(StoreNotificationRequest $request)
    {
        //
    }


    public function show(Notification $notification)
    {
        //
    }


    // Metodo showNotifications - Mostra todas as notificaçoes do user logado na rota /menu.
    public function showNotifications()
    {
        $user = auth()->user();

        // Busca as notificaçoes com state = 0 (nao lida) do user logado.
        $notifications = Notification::with('event', 'absence', 'vacation')
            ->where('state', 0)
            ->where('user_id', $user->id)
            ->get();

        return view('pages.notifications.show', compact('notifications'));
    }


    // Metodo changeState - Muda o state para 1 (lido).
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


    public function edit(Notification $notification)
    {
        //
    }


    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        //
    }


    public function destroy(Notification $notification)
    {
        //
    }

    // Metodo unreadCount - Conta quantas notificaçoes com state = 0 (nao lida)
    public function unreadCount()
    {
        $user = auth()->user();
        $unreadCount = Notification::where('state', 0)
            ->where('user_id', $user->id)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

}