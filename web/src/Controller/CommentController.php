<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Handler\PostCommentHandler;

class CommentController extends AbstractController
{
    public function __construct(
        private PostCommentHandler $postCommentHandler
    ) {
    }


    #[Route(
        name: 'api_comment_post',
        path: '/post/comment',
        methods: ['POST']
    )]
    public function __invoke(Comment $data): Comment
    {
        $data->setUser($this->getUser());
        $this->postCommentHandler->handle($data);
        return $data;
    }

    #[Route('/post/comment/{id}', name: 'api_post_comment_detail')]
    public function index(Request $request, Comment $id, CommentRepository $commentRepository): Response
    {
        $comment = $commentRepository->find($id);

        return  $this->render('blog/post.html.twig', [
            'comment' => $comment
        ]);
    }
}
