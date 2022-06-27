<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function index()
    {

        return view('friends.index', [
            'friends' => auth()->user()->friends()
        ]);
    }

}
