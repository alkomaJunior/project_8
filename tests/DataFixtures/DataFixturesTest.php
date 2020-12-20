<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\DataFixtures;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

class DataFixturesTest extends KernelTestCase
{
    private ?Application $application;

    protected function setUp(): void
    {
        self::bootKernel();
        self::$kernel = static::createKernel();
        $this->application = new Application(self::$kernel);
        $this->runCommand(
            'doctrine:database:drop',
            ['--force' => true]
        );
    }

    public function testExecute(): void
    {
        $createDb = $this->runCommand('doctrine:database:create');
        $this->commandHasError(
            $createDb,
            'Created database '.self::$kernel->getProjectDir().'\var\cache\test/test.db for connection named default',
            'Database should be created'
        );

        $schemaUpdate = $this->runCommand('doctrine:schema:update', ['--force' => true]);
        $this->commandHasError(
            $schemaUpdate,
            '[OK] Database schema updated successfully!',
            'Schema should be updated'
        );

        $loadFixtures = $this->runCommand('doctrine:fixtures:load', ['--append' => true]);
        $this->commandHasError(
            $loadFixtures,
            '> loading App\DataFixtures\AppFixtures',
            'Fixtures should be loaded'
        );

        $this->fixturesGoodLoaded();
    }

    /**
     * Check if Db contains the same number of users & tasks in fixtures files.
     */
    protected function fixturesGoodLoaded(): void
    {
        $storedTasks = self::$container->get(TaskRepository::class)->findTasks();
        $storedUsers = self::$container->get(UserRepository::class)->findAll();

        $users = $this->getDataFixture('User');
        $tasks = $this->getDataFixture('Task');

        $this->assertEquals(count($tasks), count($storedTasks));
        $this->assertEquals(count($users), count($storedUsers));
    }

    protected function tearDown(): void
    {
        $this->runCommand(
            'doctrine:database:drop',
            ['--force' => true]
        );
        $this->application = null;
        self::ensureKernelShutdown();
    }

    protected function runCommand(string $command, array $args = []): CommandTester
    {
        $input = array_merge(['command' => $command], $args);
        $command = $this->application->find($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute($input);

        return $commandTester;
    }

    protected function commandHasError(CommandTester $commandTester, string $stringContains, string $message): void
    {
        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString(
            $stringContains,
            $commandTester->getDisplay(),
            $message
        );
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getDataFixture(string $fileName): array
    {
        return Yaml::parse(
            file_get_contents(
                self::$kernel->getProjectDir().'/src/DataFixtures/Fixtures/'.$fileName.'s.yaml',
                true
            )
        );
    }
}
