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
        $notifications = Notification::with(['event', 'absence', 'vacation'])->where('state', false)->get();

        // Passe as notificações para a view
        return view('pages.notifications.show', ['notifications' => $notifications]);
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
        $notifications = Notification::with(['event', 'absence', 'vacation'])->get();

        dd($notifications);
        return view('pages.notifications.show', ['notifications' => $notifications]);
    }



    public function showNotifications()
    {
        $notifications = Notification::with('event', 'absence', 'vacation')
            ->where('state', 0) // Exemplo de condição
            ->get();

        return view('pages.notifications.show', compact('notifications'));
    }

    public function changeState(Request $request)
    {

        // Recupera todas as notificações enviadas
        $notifications = $request->input('notifications', []);

        // Itera sobre cada notificação para processá-la
        foreach ($notifications as $notificationData) {
            // Verifica se o ID da notificação está definido
            if (isset($notificationData['id'])) {
                // Atualiza o estado da notificação no banco de dados
                \DB::table('notifications')
                    ->where('id', $notificationData['id'])
                    ->update(['state' => 1]);
            }
        }

        // Redireciona de volta para a página de notificações com uma mensagem de sucesso
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