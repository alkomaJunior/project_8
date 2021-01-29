<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\PHPUnit\Helper\TestPrivateMethodTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FixturesLoadingTest extends KernelTestCase
{
    use TestPrivateMethodTrait;

    private ?Application $application;
    private ?AppFixtures $appFixtures;

    protected function setUp(): void
    {
        self::bootKernel();
        self::$kernel = static::createKernel();
        $this->application = new Application(self::$kernel);

        $this->appFixtures = new AppFixtures();
    }

    public function testExecute(): void
    {
        $this->runCommand(
            'doctrine:database:drop',
            ['--force' => true]
        );
        $createDb = $this->runCommand('doctrine:database:create');
        $this->commandHasError(
            $createDb,
            'Created database '.self::$kernel->getProjectDir().DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache'.
            DIRECTORY_SEPARATOR.'test/test.db for connection named default',
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

    protected function tearDown(): void
    {
        $this->runCommand(
            'doctrine:database:drop',
            ['--force' => true]
        );
        $this->application = null;
        $this->appFixtures = null;
        self::ensureKernelShutdown();
    }

    /**
     * Check if Db contains the same number of users & tasks in fixtures files.
     */
    protected function fixturesGoodLoaded(): void
    {
        $storedTasks = self::$container->get(TaskRepository::class)->findTasks();
        $storedUsers = self::$container->get(UserRepository::class)->findAll();

        $users = $this->invokeMethod($this->appFixtures, 'getDataFixture', ['User']);
        $tasks = $this->invokeMethod($this->appFixtures, 'getDataFixture', ['Task']);
        $emptyArray = $this->invokeMethod($this->appFixtures, 'getDataFixture', ['Wrong_class']);

        $this->assertEquals(count($emptyArray), 0);
        $this->assertEquals(count($tasks), count($storedTasks));
        $this->assertEquals(count($users), count($storedUsers));
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
}
