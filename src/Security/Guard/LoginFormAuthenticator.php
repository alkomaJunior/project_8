<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Security\Guard;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * Class LoginFormAuthenticator.
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    public const LOGIN_ROUTE = 'login';

    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param UrlGeneratorInterface        $urlGenerator
     * @param CsrfTokenManagerInterface    $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod(Request::METHOD_POST);
    }

    /**
     * {@inheritdoc}
     *
     * @return string[] $credentials
     */
    public function getCredentials(Request $request): array
    {
        $parametersBag = $request->request;
        $credentials = [
            'username' => $parametersBag->get('_username'),
            'password' => $parametersBag->get('_password'),
            'csrf_token' => $parametersBag->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    /**
     * {@inheritdoc}
     *
     * @param string[] $credentials
     *
     * @throws InvalidCsrfTokenException|AuthenticationException
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            // Fail authentication with an error
            throw new InvalidCsrfTokenException();
        }

        /** @var UserInterface|null $user */
        $user = $userProvider->loadUserByUsername($credentials['username']);

        if (!$user) {
            // Fail authentication with a custom error
            throw new AuthenticationException('User could not be found.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     *
     * @param string[] $credentials
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            return true;
        }

        // Fail authentication with an UsernameNotFoundError if password doesn't match
        throw new AuthenticationException();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $providerKey
     *
     * @return RedirectResponse | null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }
}
