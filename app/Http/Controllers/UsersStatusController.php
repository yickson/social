<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Resources\StatusResource;

class UsersStatusController extends Controller
{
    public function index(User $user)
    {
        return StatusResource::collection(
            $user->statuses()->latest()->paginate()
        );
    }
}
