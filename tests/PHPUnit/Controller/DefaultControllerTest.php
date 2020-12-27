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
use App\Tests\PHPUnit\Helper\LoginTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use FixturesTrait;
    use LoginTrait;

    protected ?kernelBrowser $client;
    /** @var User[] */
    protected ?array $users = [];

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->users = $this->loadFixtureFiles([dirname(__DIR__).'/fixtures/users.yaml']);
    }

    public function testHomepageVisitor(): void
    {
        $this->client->request('GET', '/');

        $this->assertSelectorExists("form[action='/login']", 'login form should exist');
        $this->assertSelectorExists('h5', 'H5 should exist');
        $this->assertSelectorNotExists('a[href="/users/create"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorNotExists('a[href="/tasks/create"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorNotExists(
            'a[href="/tasks/done/true"]',
            'The button with link "/users/create" should exist'
        );
        $this->assertSelectorNotExists(
            'a[href="/tasks/done/false"]',
            'The button with link "/users/create" should exist'
        );
    }

    public function testHomepageLinksAdminLogged(): void
    {
        $this->logIn($this->users['user_1'], $this->client);
        $this->client->request('GET', '/');
        $this->client->getResponse();

        $this->assertSelectorExists('a[href="/users/create"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists('a[href="/tasks/create"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists('a[href="/tasks/done/false"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists('a[href="/tasks/done/true"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists("a[href='/logout']", 'Logout button should not exist');
        $this->assertSelectorNotExists('h5', 'H5 should not exist');
    }

    public function testHomepageUserLogged(): void
    {
        $this->logIn($this->users['user_2'], $this->client);
        $this->client->request('GET', '/');
        $this->client->getResponse();

        $this->assertSelectorExists("a[href='/logout']", 'Logout button should not exist');
        $this->assertSelectorNotExists('h5', 'H5 should not exist');
        $this->assertSelectorNotExists('a[href="/users/create"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists('a[href="/tasks/create"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists('a[href="/tasks/done/true"]', 'The button with link "/users/create" should exist');
        $this->assertSelectorExists('a[href="/tasks/done/false"]', 'The button with link "/users/create" should exist');
    }

    protected function tearDown(): void
    {
        $this->client = null;
        $this->users = null;
    }
}
