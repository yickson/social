<?php

namespace App\Models;

use App\User;
use App\Traits\HasLikes;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasLikes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function path()
    {
        return route('statuses.show', $this->status_id) . '#comment-' . $this->id;
    }
}
