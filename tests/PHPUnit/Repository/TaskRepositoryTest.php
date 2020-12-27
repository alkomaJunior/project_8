<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Repository;

use App\Repository\TaskRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    private ?array $fixturesTasks = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->fixturesTasks = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/tasks_users.yaml',
        ]);
    }

    public function testFindAllTasks(): void
    {
        // Retrieve registered tasks in database
        $storedTasks = $this->findTasks();
        $expectedTasksNumber = $this->getExpectedTasksNumber('task');

        $this->assertEquals($expectedTasksNumber, count($storedTasks));
    }

    public function testFindDoneTasks(): void
    {
        $tasks = $this->findTasks('true');

        $expectedTasksNumber = $this->getExpectedTasksNumber('task_done');

        $this->assertEquals($expectedTasksNumber, count($tasks));
    }

    public function testFindNotDoneTasks(): void
    {
        $tasks = $this->findTasks('false');

        $expectedTasksNumber = $this->getExpectedTasksNumber('task_not');

        $this->assertEquals($expectedTasksNumber, count($tasks));
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        $this->fixturesTasks = null;
    }

    private function findTasks(?string $isDone = null): array
    {
        return self::$container->get(TaskRepository::class)->findTasks($isDone);
    }

    private function getExpectedTasksNumber(string $expectedKey): int
    {
        $expectedTasksNumber = 0;

        foreach ($this->fixturesTasks as $key => $task) {
            if ($expectedKey === substr($key, 0, strlen($expectedKey))) {
                ++$expectedTasksNumber;
            }
        }

        return $expectedTasksNumber;
    }
}
