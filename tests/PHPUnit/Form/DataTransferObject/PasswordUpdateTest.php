<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Form\DataTranferObject;

use App\Form\DataTransferObject\PasswordUpdate;
use App\Tests\PHPUnit\Helper\HelperTrait;
use App\Tests\PHPUnit\Helper\LoginTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordUpdateTest extends WebTestCase
{
    use FixturesTrait;
    use HelperTrait;
    use LoginTrait;

    protected ?array $users;
    protected ?PasswordUpdate $updatePassword;
    protected ?kernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = $this->createClient();

        $this->users = $this->loadFixtureFiles([
            dirname(__DIR__,2).'/fixtures/users.yaml',
        ]);
        $this->updatePassword = (new PasswordUpdate())
            ->setActualPassword('test1')
            ->setNewPassword('NewPassword1-')
            ->setConfirmPassword('NewPassword1-');
    }

    public function testValidEntity(): void
    {
        $this->logIn($this->users['user_1'], $this->client);
        $this->client->request('GET', '/');

        $this->assertHasErrors($this->updatePassword, 0);
    }

    public function testInvalidEntity(): void
    {
        $this->logIn($this->users['user_1'], $this->client);
        $this->client->request('GET', '/');
        $this->updatePassword->setNewPassword('12');
        $this->assertHasErrors($this->updatePassword, 4);
        $this->updatePassword->setNewPassword('pasword1');
        $this->assertHasErrors($this->updatePassword, 3);
        $this->updatePassword->setNewPassword('Pasword1');
        $this->assertHasErrors($this->updatePassword, 2);
        $this->updatePassword->setNewPassword('Pasword1-');
        $this->assertHasErrors($this->updatePassword, 1);
        $this->updatePassword->setNewPassword('Pasword1-');
        $this->assertHasErrors($this->updatePassword, 1);
    }

    protected function tearDown(): void
    {
        $this->users = null;
        $this->updatePassword = null;
        $this->client = null;
    }
}
