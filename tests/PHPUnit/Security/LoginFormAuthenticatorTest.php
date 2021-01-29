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
use App\Security\Guard\LoginFormAuthenticator;
use Doctrine\ORM\EntityManager;
use function PHPUnit\Framework\assertIsString;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class LoginFormAuthenticatorTest extends TestCase
{
    private ?array $credentials;
    private ?UrlGenerator $urlGenerator;
    private ?CsrfTokenManager $csrfTokenManager;
    private ?UserPasswordEncoder $encoder;
    private ?LoginFormAuthenticator $formAuthenticator;
    private ?Request $request;
    private ?User $user;

    protected function setUp(): void
    {
        $this->generateData();
        $this->urlGenerator = $this->getMockBuilder(UrlGenerator::class)->disableOriginalConstructor()->getMock();
        $this->csrfTokenManager = $this->getMockBuilder(CsrfTokenManager::class)->getMock();
        $this->encoder = $this->getMockBuilder(UserPasswordEncoder::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->initializePostRequest();

        $this->formAuthenticator = new LoginFormAuthenticator(
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
        $this->assertIsArray($this->formAuthenticator->getCredentials( $this->request));
        $this->assertEquals($this->credentials, $this->formAuthenticator->getCredentials($this->request));
    }

    /*public function testGetPassword(): void
    {
        $this->assertIsArray($this->formAuthenticator->getCredentials( $this->request));
        $this->assertEquals($this->user->getPassword(), $this->formAuthenticator->getPassword
        ($this->formAuthenticator->getCredentials($this->request)));
    }*/

    public function testGetUserValid(): void
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $this->csrfTokenManager->expects($this->once())->method('isTokenValid')->willReturn(true);
        $userProvider->expects($this->once())->method('loadUserByUsername')->willReturn(new User());
        $this->assertInstanceOf(User::class, $this->formAuthenticator->getUser($this->credentials, $userProvider));
    }

    public function testGetUserThrowingInvalidCsrfTokenException(): void
    {
        $this->credentials['csrf_token'] = 'Invalid_token';
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $this->csrfTokenManager->expects($this->once())->method('isTokenValid')->willReturn(false);
        $userProvider->expects($this->never())->method('loadUserByUsername')->willReturn(new User());
        $this->expectException(InvalidCsrfTokenException::class);

        $this->formAuthenticator->getUser($this->credentials, $userProvider);
    }

    public function testGetUserThrowingAuthenticationException(): void
    {
        $this->credentials['username'] = 'username-not-found';
        $this->credentials['csrf_token'] = 'Invalid_token';

        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $this->csrfTokenManager->expects($this->once())->method('isTokenValid')->willReturn(true);
        $userProvider->expects($this->once())->method('loadUserByUsername')->willReturn(null);
        $this->expectException(AuthenticationException::class);
        $this->formAuthenticator->getUser($this->credentials, $userProvider);
    }

    public function testCheckCredentials(): void
    {
        $this->encoder->expects($this->once())->method('isPasswordValid')->willReturn(true);

        $this->assertTrue($this->formAuthenticator->checkCredentials(
            $this->credentials, $this->user));
    }

    public function testCheckCredentialsThrowingExceptionAuthenticationException(): void
    {
        $this->credentials['username'] = 'username-not-found';

        $this->encoder->expects($this->once())->method('isPasswordValid')->willReturn(false);
        $this->expectException(AuthenticationException::class);
        $this->formAuthenticator->checkCredentials($this->credentials, $this->user);
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

    protected function tearDown(): void
    {
        $this->urlGenerator = null;
        $this->csrdToken = null;
        $this->encoder = null;
        $this->formAuthenticator = null;
        $this->credentials = null;
        $this->user = null;
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
            ['_route' => 'homepage']
        );

        $session = new Session(new MockArraySessionStorage());
        $request->setMethod(Request::METHOD_POST);
        $request->setSession($session);

        return $request;
    }

    private function generateData(): void
    {
        $this->user = (new User())->setUsername('username-1')->setPassword('username-1');
        $token = new UsernamePasswordToken(
            $this->user,
            null,
            'main',
            $this->user->getRoles()
        );
        $this->credentials = [
            'username' => $this->user->getUsername(),
            'password' => $this->user->getPassword(),
            'csrf_token' => $token,
        ];
    }
}
