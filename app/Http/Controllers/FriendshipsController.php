<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class FriendshipsController extends Controller
{
    public function store(User $recipient)
    {
        if (auth()->id() === $recipient->id)
        {
            abort(400);
        }
        $friendship = Friendship::query()->firstOrCreate([
            'sender_id' => auth()->id(),
            'recipient_id' => $recipient->id
        ]);

        return response()->json([
            'friendship_status' => $friendship->fresh()->status
        ]);

    }

    public function destroy(User $user)
    {
        $friendship = Friendship::query()->betweenUsers(auth()->user(), $user)->first(); //scope solicitudes entre usuarios(usuario_autenticado, usuario por parametro)

        if ($friendship->status === 'denied' && (int) $friendship->sender_id === auth()->id())
        {
            return response()->json([
                'friendship_status' => 'denied'
            ]);
        }

        return response()->json([
            'friendship_status' => $friendship->delete() ? 'deleted' : ''
        ]);

    }
}
