<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(Request $request, PaginatorInterface $paginator, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        $posts = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            10
        );

        return  $this->render('blog/index.html.twig', [
            'posts' => $posts
        ]);
    }


    #[Route('/blog/{id}', name: 'app_blog_id')]
    public function postById(Request $request, Post $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);

        return  $this->render('blog/post.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/post/comment', name: 'post_comment', methods: ['POST'])]
    public function postComment(): Response
    {
        return  $this->render('blog/index.html.twig', [
        ]);
    }
}
