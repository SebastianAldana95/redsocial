<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function show(User $user)
    {
        $friendshipStatus = optional(Friendship::query()->where([
            'recipient_id' => $user->id,
            'sender_id' => auth()->id()
        ])->first())->status; // si no encuentra una solicitud, devulve null

        return view('users.show', compact('user', 'friendshipStatus'));
    }
}
