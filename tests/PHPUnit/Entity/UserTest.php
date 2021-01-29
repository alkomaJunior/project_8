<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Entity;

use App\Entity\User;
use App\Tests\PHPUnit\Helper\HelperTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use FixturesTrait;
    use HelperTrait;

    private ?User $user;

    protected function setUp(): void
    {
        $this->user = (new User())
            ->setUsername('username')
            ->setEmail('username@text.de')
            ->setPassword('1234Test*');
    }

    public function testValidEntity(): void
    {
        $user = $this->user;
        $this->assertHasErrors($user, 0);
    }

    public function testInvalidPassword(): void
    {
        $this->assertHasErrors($this->user->setPassword(''), 2);
        $this->assertHasErrors($this->user->setPassword('t'), 4);
        $this->assertHasErrors($this->user->setPassword('T1'), 3);
        $this->assertHasErrors($this->user->setPassword('T1v'), 2);
        $this->assertHasErrors($this->user->setPassword('T1v1*'), 1);
    }

    public function testInvalidUsername(): void
    {
        $this->assertHasErrors($this->user->setUsername('78username?'), 2);
        $this->assertHasErrors($this->user->setUsername('78username'), 1);
        $this->assertHasErrors($this->user->setUsername('username?'), 1);
    }

    public function testInvalidEmail(): void
    {
        $this->assertHasErrors($this->user->setEmail('mail.de'), 1);
    }

    public function testInvalidRoles(): void
    {
        $this->assertHasErrors($this->user->setRoles(['ROLE_INVALID']), 1);
    }

    public function testInvalidUsedUsername(): void
    {
        $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/users.yaml',
        ]);
        $this->assertHasErrors($this->user->setUsername('username-1'), 1);
    }

    public function testInvalidUsedEmail(): void
    {
        $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/users.yaml',
        ]);
        $this->assertHasErrors($this->user->setUsername('test-1@todo.de'), 1);
    }

    protected function tearDown(): void
    {
        $this->user = null;
    }
}
