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

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidFlushedEntityTest extends KernelTestCase
{
    use FixturesTrait;

    private ?array $dbData;

    protected function setUp(): void
    {

        $this->dbData = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/tasks_users.yaml',
        ]);
    }

    public function testValidFlushedTask(): void
    {
        /** @var Task $task */
        $task = $this->dbData['task_not_done_1'];

        $this->assertEquals(1, $task->getId());
        $this->assertEquals('title-1', $task->getTitle());
        $this->assertEquals('content-1', $task->getContent());
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
        $this->assertInstanceOf(User::class, $task->getUser());
    }

    public function testValidFlushedUser(): void
    {
        /** @var User $user */
        $user = $this->dbData['user_1'];

        $this->assertEquals(1, $user->getId());
        $this->assertEquals('username-1', $user->getUsername());
        $this->assertEquals('test-1@todo.de', $user->getEmail());
        $this->assertIsString($user->getPassword());
        $this->assertIsArray($user->getRoles());
        $this->assertContains(User::ROLE_ADMIN, $user->getRoles(), "\nRoles doesn't contains ROLE_USER");
    }

    protected function tearDown(): void
    {
        $this->dbData = null;
    }
}
