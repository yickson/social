<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Events\CommentCreated;
use App\Http\Resources\CommentResource;

class StatusCommentsController extends Controller
{
    public function store(Status $status)
    {
        request()->validate([
            'body' => 'required'
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'status_id' => $status->id,
            'body' => request('body')
        ]);

        $commentResource = CommentResource::make($comment);

        CommentCreated::dispatch($commentResource);

        return $commentResource;
    }
}
