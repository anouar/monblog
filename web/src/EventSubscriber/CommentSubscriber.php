<?php

namespace App\EventSubscriber;

use App\Events\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommentSubscriber implements EventSubscriberInterface
{
    public function __construct(public MailerInterface $mailer, public UrlGeneratorInterface $urlGenerator, public $sender)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CommentEvent::class => 'commentCreated',
        ];
    }

    public function commentCreated(CommentEvent $event): void
    {
        $comment = $event->getComment();
        $post = $comment->getPost();

        $linkPost = $this->urlGenerator->generate('app_blog_id', [
            'id' => $post->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $body =  [
            'title' => $post->getTitle(),
            'link' => $linkPost,
        ];

        $email = (new Email())
            ->from($this->sender)
            ->to($post->getUser()->getEmail())
            ->subject('article commentée')
            ->html('<p>Lorem ipsum...</p>')
        ;
        $this->mailer->send($email);
    }
}
