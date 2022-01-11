<?php

namespace Givebutter\Tests\Support;

use Illuminate\Http\Request;
use Givebutter\Tests\Support\Post;

class PostsController
{
    public function show(Request $request, Post $post)
    {
        return response('All good', 200);
    }
}
