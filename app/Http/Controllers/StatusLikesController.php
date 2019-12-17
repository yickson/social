<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusLikesController extends Controller
{
    public function store(Status $status)
    {
        $status->like();

        return response()->json([
            'likes_count' => $status->likesCount()
        ]);
    }

    public function destroy(Status $status)
    {
        $status->unlike();

        return response()->json([
            'likes_count' => $status->likesCount()
        ]);
    }
}
