<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusCommentsController extends Controller
{
    public function store(Status $status)
    {
        \request()->validate([
            'body' => 'required'
        ]);

        $comment = Comment::query()->create([
            'user_id' => auth()->id(),
            'status_id' => $status->id,
            'body' => \request('body')
        ]);

        return CommentResource::make($comment);
    }
}
