<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;

class AcceptFriendshipsController extends Controller
{
    public function index()
    {
        $friendshipRequests = Friendship::query()->with('sender')->where([
            'recipient_id' => auth()->id(),
        ])->get();

        return view('friendships.index', compact('friendshipRequests'));
    }

    public function store(User $sender)
    {
        Friendship::query()->where([
            'sender_id' => $sender->id,
            'recipient_id' => auth()->id(),
        ])->update(['status' => 'accepted']);

        return response()->json([
            'friendship_status' => 'accepted'
        ]);
    }

    public function destroy(User $sender)
    {
        Friendship::query()->where([
            'sender_id' => $sender->id,
            'recipient_id' => auth()->id(),
        ])->update(['status' => 'denied']);

        return response()->json([
            'friendship_status' => 'denied'
        ]);
    }
}
