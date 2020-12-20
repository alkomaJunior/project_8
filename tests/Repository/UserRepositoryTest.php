<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    private ?array $fixturesUsers = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->fixturesUsers = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/users.yaml',
        ]);
    }

    public function testFindAllExceptLoggedUser(): void
    {
        // Retrieve all stored users in database except logged user
        $dbRegisteredUsers = self::$container->get(UserRepository::class)->findAllExceptOne(1);

        $this->assertEquals(count($this->fixturesUsers) - 1, count($dbRegisteredUsers));
    }

    protected function tearDown(): void
    {
        $this->fixturesUsers = null;
    }
}
