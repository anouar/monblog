<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Events\CommentEvent;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(public PostRepository $postRepository, public CommentRepository $commentRepository)
    {
    }

    #[Route('/blog', name: 'app_blog')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $this->postRepository->findAll();
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
    public function postById(Request $request, Post $id): Response
    {
        $post = $this->postRepository->find($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $comments = $this->commentRepository->findBy(['post' => $post]);
        return  $this->render('blog/post.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }

    #[Route('/post/{id}/comment', name: 'post_comment', methods: ['POST'])]
    public function postComment(Request $request, Post $id, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $post = $this->postRepository->find($id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTime());
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $eventDispatcher->dispatch(new CommentEvent($comment));
            $this->addFlash('success', 'votre commentaire a été publié');
            return $this->redirectToRoute('app_blog_id', ['id' => $post->getId()]);
        }

        return $this->render('blog/post.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
