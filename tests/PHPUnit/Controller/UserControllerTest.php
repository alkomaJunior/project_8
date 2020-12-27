<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Helper\FormTrait;
use App\Tests\Helper\LoginTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use LoginTrait;
    use FixturesTrait;
    use FormTrait;

    protected ?kernelBrowser $client = null;
    /**
     * @var User[]
     */
    protected array $users = [];

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->users = $this->loadFixtureFiles([dirname(__DIR__).'/fixtures/Users.yaml']);
    }

    public function testCreateInvalidUser(): void
    {
        $uri = '/users/create';
        $data = [
            'user[username]' => 'test-username',
            'user[email]' => 'test-username@mail.de',
            'user[password][first]' => 'Passwordtest123',
            'user[password][second]' => 'Passwordtest123',
        ];

        $this->logIn($this->users['user_1'], $this->client);
        $this->submitForm($this->client, "form[action='".$uri."']", $data, $uri);

        $this->assertSelectorTextContains(
            '.form-error-message',
            'Votre mot de passe devrait contenir au moin un caractère spécial!'
        );
    }

    public function testCreateUser(): void
    {
        $uri = '/users/create';
        $data = [
            'user[username]' => 'test-username',
            'user[email]' => 'test-username@mail.de',
            'user[password][first]' => 'Passwordtest*123',
            'user[password][second]' => 'Passwordtest*123',
            'user[roles]' => User::ROLE_ADMIN,
            ];

        $this->logIn($this->users['user_1'], $this->client);
        $this->submitForm($this->client, "form[action='".$uri."']", $data, $uri);

        $this->formHasError($this->client, '/users');
        $this->assertSelectorTextContains('.alert-success > span', "L'utilisateur a bien été ajouté.");
        $this->assertSelectorTextContains('td', 'test-username');
    }

    public function testEditUser(): void
    {
        $uri = '/users/10/edit';
        $httpReferer = '/users';
        $formSelector = "form[action='".$uri."']";
        $data = [
            'account[username]' => 'username-changed',
        ];

        $this->logIn($this->users['user_1'], $this->client);
        $this->submitForm($this->client, $formSelector, $data, $uri);

        $this->formHasError($this->client, $httpReferer);
        $this->assertSelectorTextContains('.alert-success > span', "L'utilisateur a bien été modifié.");
        $this->assertSelectorTextContains('td', 'username-changed');
    }

    public function testLoggedUserEditHisAccount(): void
    {
        $uri = '/users/2/edit';
        $httpReferer = '/';
        $formSelector = "form[action='".$uri."']";
        $data = [
            'account[username]' => 'username-changed',
        ];

        $this->logIn($this->users['user_2'], $this->client);
        $this->submitForm($this->client, $formSelector, $data, $uri);

        $this->formHasError($this->client, $httpReferer);
        $this->assertSelectorTextContains('.alert-success > span', "L'utilisateur a bien été modifié.");
        $this->assertSelectorExists("img[alt='username-changed']");
        $this->assertSelectorTextContains('#navbarDropdown', 'username-changed');
    }

    public function testEditUsersPassword(): void
    {
        $uri = '/users/3/edit-password';
        $httpReferer = '/users';
        $formSelector = "form[action='".$uri."']";
        $data = [
            'edit_password[password][first]' => '123*Password',
            'edit_password[password][second]' => '123*Password',
        ];

        $this->logIn($this->users['user_1'], $this->client);

        $this->submitForm($this->client, $formSelector, $data, $uri);

        $this->formHasError($this->client, $httpReferer);
        $this->assertSelectorTextContains('.alert-success > span', 'Le mot de passe a bien été modifié.');
    }

    public function testLoggedUserEditHisPassword(): void
    {
        $uri = '/users/3/edit-password';
        $httpReferer = '/';
        $formSelector = "form[action='".$uri."']";
        $data = [
            'edit_password[password][first]' => '123*Password',
            'edit_password[password][second]' => '123*Password',
        ];

        $this->logIn($this->users['user_3'], $this->client);

        $this->submitForm($this->client, $formSelector, $data, $uri);

        $this->formHasError($this->client, $httpReferer);
        $this->assertSelectorTextContains('.alert-success > span', 'Le mot de passe a bien été modifié.');
    }

    protected function tearDown(): void
    {
        unset($this->client);
        unset($this->user);
    }
}
