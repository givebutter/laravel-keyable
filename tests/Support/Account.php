<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelKeyable\Keyable;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use Keyable;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
