<?php

namespace Givebutter\Tests\Support;

use Givebutter\Tests\Support\Post;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
