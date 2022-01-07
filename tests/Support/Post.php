<?php

namespace Givebutter\Tests\Support;

use Givebutter\Tests\Support\Account;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
