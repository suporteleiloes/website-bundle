<?php

namespace SL\WebsiteBundle\Security;

use SL\WebsiteBundle\Services\ApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class ApiAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private $router;
    private $apiService;

    public function __construct(RouterInterface $router, ApiService $apiService)
    {
        $this->router = $router;
        $this->apiService = $apiService;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        try {
            $userData = $this->apiService->requestToken($username, $password);
        } catch (\Throwable $exception) {
            throw new CustomUserMessageAuthenticationException($exception->getMessage());
        }

        dump($userData);

        $passport = new SelfValidatingPassport(
            new UserBadge($username, function ($userIdentifier) use ($userData) {
                return new InMemoryUser($userIdentifier, null, $userData['user']['roles'], true, true, true, true, $userData);
            }),
            [
                new CsrfTokenBadge(
                    'authenticate',
                    $request->request->get('_csrf_token')
                ),
                //(new RememberMeBadge())->enable(),
            ]);

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($target = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }
        return new RedirectResponse(
            $this->router->generate('teste')
        );
    }

    protected function getLoginUrl(Request $request): string
    {
        return '/login';
    }
}