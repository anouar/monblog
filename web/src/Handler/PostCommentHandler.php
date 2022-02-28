<?php

namespace App\Handler;

use App\Entity\Book;
use App\Entity\Comment;

class PostCommentHandler
{
    public function handle(Comment $comment): array
    {
        return ['id' => $comment->getId()];
    }
}
