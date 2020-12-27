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
use App\Tests\PHPUnit\Helper\HelperTrait;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    use HelperTrait;

    /**
     * @var Task|null
     */
    private Task $task;

    protected function setUp(): void
    {
        $this->task = (new Task())
            ->setTitle('Test')
            ->setContent('Test Content')
            ->setCreatedAt(new DateTime())
            ->setUser(null);
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->task, 0);

        $this->assertEquals('Test', $this->task->getTitle());
        $this->assertEquals('Test Content', $this->task->getContent());
    }

    public function testInvalidTitle(): void
    {
        $this->assertHasErrors($this->task->setTitle(''), 1);
    }

    public function testInvalidContent(): void
    {
        $this->assertHasErrors($this->task->setContent(''), 1);
    }

    public function testIsDoneValue(): void
    {
        $this->assertNotTrue($this->task->isDone());
        $this->assertSame('false', $this->task->urlIsDoneValue());

        $this->task->toggle(true);
        $this->assertTrue($this->task->isDone());
    }

    public function testLinkTaskToUser(): void
    {
        $user = (new User())
            ->setUsername('username')
            ->setEmail('nail@todo.de');

        $this->assertNull($this->task->getUser());

        $this->task->setUser($user);
        $this->assertIsObject($this->task->getUser());
    }

    protected function tearDown(): void
    {
        unset($this->task);
    }
}
