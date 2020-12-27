<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Helper\LoginTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SmokeTest extends WebTestCase
{
    use FixturesTrait;
    use LoginTrait;

    const TASK_URIS = [
        'task_list_all' => ['List of all tasks', '/tasks'],
        'task_list_done' => ['List of done tasks', '/tasks/done/true'],
        'task_list_not_done' => ['List of not done tasks', '/tasks/done/false'],
        'task_create' => ['Create new task', '/tasks/create'],
        'task_edit' => ['Edit task', '/tasks/1/edit'],
        'task_toggle' => ['task_toggle', '/tasks/1/toggle', '/tasks'],
        'task_delete' => ['Delete task', '/tasks/1/delete', '/tasks'],
    ];
    const USER_URIS = [
        'user_list' => ['List of users', '/users'],
        'user_create' => ['Create new user', '/users/create'],
        'user_user' => ['Edit user', '/users/1/edit'],
        'user_password_edit' => ['Edit user password', '/users/1/edit-password'],
    ];

    const UNRESTRICTED_URIS = [
        'homepage' => ['Homepage', '/'],
        'login' => ['Login page', '/login'],
    ];

    private ?KernelBrowser $client = null;
    /** @var User[]|Task[] */
    private ?array $data = [];

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->data = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/tasks_users.yaml',
        ]);
    }

    /**
     * @dataProvider provideRestrictedUrls
     *
     * @param string $pageName
     * @param string $uri
     */
    public function testRestrictedPageRedirectToLoginPage(string $pageName, string $uri): void
    {
        $this->client->request('GET', $uri);

        $this->assertResponseRedirects(
            $this->client->getRequest()->getSchemeAndHttpHost().'/login',
            Response::HTTP_FOUND,
            sprintf(
                'La page ne "%s" devrait pas être accessible, mais le code HTTP est "%s".',
                $pageName,
                $this->client->getResponse()->getStatusCode()
            )
        );
    }

    /**
     * @dataProvider provideUnrestrictedUrls
     *
     * @param string $pageName
     * @param string $uri
     */
    public function testUnrestrictedPageIsSuccessfulRendered(string $pageName, string $uri): void
    {
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse();

        self::assertTrue(
            $response->isSuccessful(),
            sprintf(
                'La page "%s" devrait être accessible, mais le code HTTP est "%s".',
                $pageName,
                $response->getStatusCode()
            )
        );
    }

    /**
     * @dataProvider provideRestrictedUrlsForUserRole
     *
     * @param string $pageName
     * @param string $uri
     */
    public function testRestrictedPagesForRoleUser(string $pageName, string $uri): void
    {
        $this->logIn($this->data['user_2'], $this->client);

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            sprintf(
                'La page ne "%s" devrait pas être accessible, mais le code HTTP est "%s".',
                $pageName,
                $this->client->getResponse()->getStatusCode()
            )
        );
    }

    public function testUserAccountSuccessfulAccess(): void
    {
        $this->logIn($this->data['user_2'], $this->client);

        $this->client->request('GET', '/users/2/edit');
        $response = $this->client->getResponse();

        self::assertTrue(
            $response->isSuccessful(),
            sprintf(
                'La page "User Account" devrait être accessible, mais le code HTTP est "%s".',
                $response->getStatusCode()
            )
        );
    }

    /**
     * @dataProvider provideRestrictedUrls
     *
     * @param string $pageName
     * @param string $uri
     * @param string $httpReferer
     */
    public function testRestrictedPagesSuccessfulAccess(string $pageName, string $uri, string $httpReferer = null): void
    {
        $this->logIn($this->data['user_1'], $this->client);
        $this->client->request('GET', $uri, [], [], ['HTTP_REFERER' => $httpReferer]);

        $response = $this->client->getResponse();
        $message = sprintf(
            'La page "%s" devrait être accessible, mais le code HTTP est "%s".',
            $pageName,
            $this->client->getResponse()->getStatusCode()
        );

        if (!$httpReferer) {
            $this->assertTrue(
                $response->isSuccessful(),
                $message
            );
        }
        if ($httpReferer) {
            $this->assertResponseRedirects(
                $httpReferer,
                Response::HTTP_FOUND,
                $message
            );
        }
    }

    public function provideRestrictedUrls(): array
    {
        return array_merge(self::USER_URIS, self::TASK_URIS);
    }

    public function provideUnrestrictedUrls(): array
    {
        return self::UNRESTRICTED_URIS;
    }

    public function provideRestrictedUrlsForUserRole(): array
    {
        return self::USER_URIS;
    }

    protected function tearDown(): void
    {
        unset($this->client);
        unset($this->data);
    }
}
