<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Friendship;

class UsersController extends Controller
{
    public function show(User $user)
    {
        $friendshipStatus = optional(Friendship::where([
            'recipient_id' => $user->id,
            'sender_id' => auth()->id()
        ])->first())->status;

        return view('users.show', compact('user', 'friendshipStatus'));
    }
}
