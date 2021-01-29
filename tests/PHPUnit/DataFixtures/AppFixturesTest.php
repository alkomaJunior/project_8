<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2021.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\Task;
use App\Entity\User;
use App\Tests\PHPUnit\Helper\TestPrivateMethodTrait;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\LogicException;

class AppFixturesTest extends KernelTestCase
{
    use TestPrivateMethodTrait;

    private ?AppFixtures $appFixtures;

    protected function setUp(): void
    {
        $this->appFixtures = new AppFixtures();
    }

    public function testCreateEntityThroughLogicException()
    {
        $this->expectException(LogicException::class);

        $this->invokeMethod($this->appFixtures, 'createEntity', ['WrongName',[]]);
    }

    public function testCreateEntity()
    {
        $properties = ['title' => 'title', 'content' => 'content'];
        $result = $this->invokeMethod(
            $this->appFixtures,
            'createEntity',
            [
                Task::class,
                $properties,
            ]
        );
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($properties['title'], $result->getTitle());
        $this->assertEquals($properties['content'], $result->getContent());
    }

    public function testHydrateEntity()
    {
        $attributes = ['title' => 'title', 'createdAt' => 'NOW()'];
        $task = new Task();
        $this->invokeMethod(
            $this->appFixtures,
            'hydrateEntity',
            [
                Task::class,
                'title',
                $task,
                $attributes['title'],
            ]
        );

        $this->assertEquals($attributes['title'], $task->getTitle());
        $task = new Task();
        $this->invokeMethod(
            $this->appFixtures,
            'hydrateEntity',
            [
                Task::class,
                'createdAt',
                $task,
                $attributes['createdAt'],
            ]
        );
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
    }

    public function testPersistFixtures()
    {
        $users = $this->invokeMethod($this->appFixtures, 'getDataFixture', ['User']);
        $objectManager = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $appFixture = $this->getMockBuilder(AppFixtures::class)->disableOriginalConstructor()->getMock();

        $appFixture->expects($this->exactly(2))->method('hasReference');
        $appFixture->expects($this->exactly(2))->method('addReference');
        $objectManager->expects($this->exactly(2))->method('persist');

        $flushedEntityNumber = $this->invokeMethod(
            $appFixture,
            'persistFixtures',
            [$objectManager, $users, User::class]
        );

        $this->assertEquals(count($users), $flushedEntityNumber);
    }

    protected function tearDown(): void
    {
        $this->appFixtures = null;
    }
}

