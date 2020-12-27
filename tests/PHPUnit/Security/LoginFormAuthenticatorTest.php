<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Security;

use App\Entity\User;
use App\Security\DataTransferObject\Credentials;
use App\Security\Guard\LoginFormAuthenticator;
use Doctrine\ORM\EntityManager;
use function PHPUnit\Framework\assertIsString;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class LoginFormAuthenticatorTest extends TestCase
{
    private ?EntityManager $entityManager;
    private ?UrlGenerator $urlGenerator;
    private ?CsrfTokenManager $csrfTokenManager;
    private ?UserPasswordEncoder $encoder;
    private ?LoginFormAuthenticator$formAuthenticator;
    private ?Request $request;

    protected function setUp(): void
    {
        $this->entityManager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $this->urlGenerator = $this->getMockBuilder(UrlGenerator::class)->disableOriginalConstructor()->getMock();
        $this->csrfTokenManager = $this->getMockBuilder(CsrfTokenManager::class)->getMock();
        $this->encoder = $this->getMockBuilder(UserPasswordEncoder::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->initializePostRequest();

        $this->formAuthenticator = new LoginFormAuthenticator(
            $this->entityManager,
            $this->urlGenerator,
            $this->csrfTokenManager,
            $this->encoder
        );
    }

    public function testGetLoginUrl(): void
    {
        $this->urlGenerator->expects($this->once())->method('generate')->willReturn('http://login.de');
        assertIsString($this->formAuthenticator->getLoginUrl());
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->formAuthenticator->supports($this->request));
    }

    public function testGetCredentials(): void
    {
        $user = (new User())->setUsername('username-1')->setPassword('username-1');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $credentials = new Credentials('username-1', 'username-1', $token);
        $this->assertInstanceOf(Credentials::class, $this->formAuthenticator->getCredentials($this->request));
        $this->assertEquals($credentials, $this->formAuthenticator->getCredentials($this->request));
    }

    public function testGetUserValid(): void
    {
        $credentials = new Credentials();
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $this->csrfTokenManager->expects($this->once())->method('isTokenValid')->willReturn(true);
        $userProvider->expects($this->once())->method('loadUserByUsername')->willReturn(new User());
        $this->assertInstanceOf(User::class, $this->formAuthenticator->getUser($credentials, $userProvider));
    }

    public function testGetUserThrowingExceptionInvalidToken(): void
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $this->csrfTokenManager->expects($this->once())->method('isTokenValid')->willReturn(false);
        $userProvider->expects($this->never())->method('loadUserByUsername')->willReturn(new User());
        $this->expectException(InvalidCsrfTokenException::class);

        $this->formAuthenticator->getUser(new Credentials(), $userProvider);
    }

    public function testGetUserThrowingExceptionUsernameNotFound(): void
    {
        $credentials = new Credentials();
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $this->csrfTokenManager->expects($this->once())->method('isTokenValid')->willReturn(true);
        $userProvider->expects($this->once())->method('loadUserByUsername')->willReturn(null);
        $this->expectException(UsernameNotFoundException::class);
        $this->formAuthenticator->getUser($credentials, $userProvider);
    }

    public function testCheckCredentials(): void
    {
        $this->encoder->expects($this->once())->method('isPasswordValid')->willReturn(true);

        $this->assertTrue($this->formAuthenticator->checkCredentials(new Credentials(), new User()));
    }

    public function testCheckCredentialsThrowingExceptionUsernameNotFound(): void
    {
        $this->encoder->expects($this->once())->method('isPasswordValid')->willReturn(false);

        $this->expectException(UsernameNotFoundException::class);
        $this->formAuthenticator->checkCredentials(new Credentials(), new User());
    }

    public function testOnAuthenticationSuccess(): void
    {
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $this->urlGenerator->expects($this->once())->method('generate')->willReturn('http://login.de');

        $this->assertInstanceOf(
            RedirectResponse::class,
            $this->formAuthenticator->onAuthenticationSuccess(
                $this->request,
                $token,
                'main'
            )
        );
    }

    public function testGetPassword(): void
    {
        $credentials = new Credentials('username', 'password');

        $this->assertEquals('password', $this->formAuthenticator->getPassword($credentials));
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        $this->entityManager;
        $this->urlGenerator = null;
        $this->csrdToken = null;
        $this->encoder = null;
        $this->formAuthenticator = null;
    }

    private function initializePostRequest(): Request
    {
        $user = (new User())->setUsername('username-1')->setPassword('username-1');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $request = new Request(
            [],
            [
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword(),
            '_csrf_token' => $token,
            ],
            ['_route' => 'login']
        );

        $request->setMethod(Request::METHOD_POST);

        return $request;
    }
}
