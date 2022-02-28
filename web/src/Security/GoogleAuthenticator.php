<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GoogleAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    public function __construct(public ClientRegistry $clientRegistry, public EntityManagerInterface $em, public RouterInterface $router, public UserPasswordHasherInterface $encoder)
    {
    }

    public function supports(Request $request): bool
    {
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();

        $user = $this->em->getRepository('App:User')
            ->findOneBy(['email' => $email]);
        if (!$user) {
            $user = new User();
            $user->setEmail($googleUser->getEmail());
            $user->setUserName($googleUser->getEmail());
            $user->setFirstName($googleUser->getFirstName());
            $user->setLastName($googleUser->getLastName());
            $user->setPassword($this->encoder->hashPassword(
                $user,
                'test'
            ));
            $user->setRoles(['ROLE_USER']);
            $user->setAvatar($googleUser->getAvatar());
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }


    private function getGoogleClient(): OAuth2Client
    {
        return $this->clientRegistry
            ->getClient('google');
    }


    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse('/connect/google');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = ['message' => $exception->getMessage()];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey):RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->router->generate('app_blog'));
    }
}
