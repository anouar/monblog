<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Security $security): Response
    {
        $user =[];
        if ($this->getUser()) {
            $user = [
               'firstname' => $this->getUser()->getFirstname(),
               'lastname' => $this->getUser()->getLastname(),
               'avatar' => $this->getUser()->getAvatar(),
           ];
        }
        return  $this->render('main/index.html.twig', [
            'user' => $user
        ]);
    }
}
