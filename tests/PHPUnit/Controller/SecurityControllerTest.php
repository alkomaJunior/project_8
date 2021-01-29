<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Controller;

use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected ?kernelBrowser $client;
    protected ?array $userInputData;
    /**
     * @var User[]
     */
    protected ?array $users = [];

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        // Retrieve correct user's login data from tests/fixtures/users.yaml
        $this->userInputData = [
            'username' => 'username-1',
            'password' => 'test1',
        ];
        $this->users = $this->loadFixtureFiles([dirname(__DIR__).'/fixtures/users.yaml']);
    }

    public function testLoginForm(): void
    {
        $uri = '/';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h3', 'CRÈATION | GÈSTION | SUPPRESSION DES TÂCHES');
        $this->assertSelectorExists("form[action='".$uri."']", 'Login form should exist');
        $this->assertSelectorNotExists("a[href='/logout']", 'Logout button should not exist');
    }

    public function testLoginWithBadCredentials(): void
    {
        $uri = '/';
        $this->logInWithForm([
            'username' => 'fake-username',
            'password' => 'fake-password',
        ]);

        $this->loginHasErrors("form[action='".$uri."']", "a[href='/logout']", $uri);
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfulLoginWithForm(): void
    {
        $this->logInWithForm($this->userInputData);

        $this->loginHasErrors("a[href='/logout']", "form[action='/']", '/');
    }

    public function testSuccessfulLoginWithToken(): void
    {
        $uri = '/';
        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');

        $this->client->request('POST', $uri, [
            '_csrf_token' => $csrfToken,
            '_username' => $this->userInputData['username'],
            '_password' => $this->userInputData['password'],
        ]);

        $this->loginHasErrors("a[href='/logout']", "form[action='".$uri."']",  $uri);
    }

    public function testSuccessLogout(): void
    {
        $this->logInWithForm($this->userInputData);

        $this->loginHasErrors("a[href='/logout']", "form[action='/']", '/');

        $this->client->clickLink('Se déconnecter');

        $this->loginHasErrors("form[action='/']", "a[href='/logout']", null);
    }

    protected function tearDown(): void
    {
        $this->client = null;
        $this->users = null;
        $this->userInputData = null;
    }

    protected function logInWithForm(array $userInputData): void
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Se connecter')->form(
            [
                '_username' => $userInputData['username'],
                '_password' => $userInputData['password'],
            ]
        );
        $this->client->submit($form);
    }

    /**
     * @param string $uri
     * @param string $selectorExists
     * @param string $selectorNotExists
     */
    private function loginHasErrors(string $selectorExists, string $selectorNotExists, string $uri = null): void
    {
        $this->assertResponseRedirects($uri);

        $this->client->followRedirect();

        $this->assertSelectorExists($selectorExists, 'HTML node with "'.$selectorExists.'" should exist');
        $this->assertSelectorNotExists($selectorNotExists, 'HTML node with "'.$selectorNotExists.'" should not exist');
    }
}
