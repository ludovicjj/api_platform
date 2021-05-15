<?php


namespace App\Security;


use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    public const LOGIN_ROUTE = 'api_login';

    /** @var UserPasswordEncoderInterface $userPasswordEncoder */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            "message" => 'dd Authentication required'
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === self::LOGIN_ROUTE &&
            $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = json_decode($request->getContent(), true);

        return [
            'username' => $credentials['username'] ?? '',
            'password' => $credentials['password'] ?? ''
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            "message" => $exception->getMessageKey()
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        /** @var User $user */
        $user = $token->getUser();
        $apiKey = null;

        if ($user->getApiKey()) {
            $apiKey = $user->getApiKey()->getApiKey();
        }

        $data = [
            "token" => $apiKey
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}