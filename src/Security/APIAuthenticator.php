<?php

namespace App\Security;

use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;


class APIAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $identifier = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        return new SelfValidatingPassport(     
            new UserBadge($identifier)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function handleAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
{
    return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
}

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
    $jsonResponse = $this->handleAuthenticationFailure($request, $exception);    
    return new Response($jsonResponse->getContent(), $jsonResponse->getStatusCode());
    }
}