<?php

namespace Givebutter\Tests\Support;

use Givebutter\Tests\Support\Account;
use Givebutter\Tests\Support\Comment;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
