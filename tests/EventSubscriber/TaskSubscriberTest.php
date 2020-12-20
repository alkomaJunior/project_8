<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\EventSubscriber;

use App\Entity\Task;
use App\Entity\User;
use App\Event\TaskSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Security;

class TaskSubscriberTest extends TestCase
{
    private ?Security $security;
    private ?TaskSubscriber $subscriber;
    private ?ObjectManager $objectManager;
    private ?Task $task;

    protected function setUp(): void
    {
        $this->task = $this->getMockBuilder(Task::class)->getMock();
        $this->security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $this->subscriber = new TaskSubscriber($this->security);
        $this->objectManager = $this->getMockBuilder(ObjectManager::class)->getMock();
    }

    public function testEventSubscription(): void
    {
        $subscribedEvents = $this->subscriber->getSubscribedEvents();

        $this->assertIsArray($subscribedEvents);
        $this->assertTrue(in_array(Events::prePersist, $subscribedEvents));
    }

    public function testSetDateAndAuthor(): void
    {
        $lifecycleEventArgs = new LifecycleEventArgs($this->task, $this->objectManager);

        $this->security->expects($this->once())->method('getUser')->willReturn(new User());
        $this->task->expects($this->once())->method('setUser')->willReturn($this->task);
        $this->task->expects($this->once())->method('setCreatedAt');

        $this->subscriber->prePersist($lifecycleEventArgs);
    }

    public function testPersistNotTaskEntity(): void
    {
        $lifecycleEventArgs = new LifecycleEventArgs(new stdClass(), $this->objectManager);

        $this->task->expects($this->never())->method('setUser');
        $this->task->expects($this->never())->method('setCreatedAt');
        $this->subscriber->prePersist($lifecycleEventArgs);
    }

    protected function tearDown(): void
    {
        $this->task = null;
        $this->security = null;
        $this->subscriber = null;
        $this->objectManager = null;
    }
}
