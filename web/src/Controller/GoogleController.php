<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck(LoggerInterface $logger): RedirectResponse
    {
        if (!$this->getUser()) {
            $logger->error('utilisateur non trouvé !');
            return $this->redirectToRoute('app_main');
        } else {
            $logger->info('SUCESS: ' . $this->getUser()->getFirstname() .' ' . $this->getUser()->getLastname()  . ' est connecté');
            return $this->redirectToRoute('app_blog');
        }
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(LoggerInterface $logger)
    {
        $logger->info('SUCESS: L\'utilisateur' . $this->getUser()->getFirstname() .' ' . $this->getUser()->getLastname()  . 'est déconnecté');
    }
}
