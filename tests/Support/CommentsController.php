<?php

namespace Givebutter\Tests\Support;

use Illuminate\Http\Request;
use Givebutter\Tests\Support\Post;

class CommentsController
{
    public function show(Request $request, Post $post, Comment $comment)
    {
        return response('All good', 200);
    }
}
