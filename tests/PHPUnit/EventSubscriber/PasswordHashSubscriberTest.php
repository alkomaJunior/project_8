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

use App\Entity\User;
use App\Event\PasswordHashSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class PasswordHashSubscriberTest extends TestCase
{
    private ?UserPasswordEncoder $encoder;
    private ?PasswordHashSubscriber $subscriber;
    private ?ObjectManager $objectManager;
    private ?PreUpdateEventArgs $preUpdateEventArgs;

    protected function setUp(): void
    {
        $this->encoder = $this->getMockBuilder(UserPasswordEncoder::class)->disableOriginalConstructor()->getMock();

        $this->subscriber = new PasswordHashSubscriber($this->encoder);

        $this->objectManager = $this->getMockBuilder(ObjectManager::class)->getMock();

        $this->preUpdateEventArgs = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testEventSubscription(): void
    {
        $subscribedEvents = $this->subscriber->getSubscribedEvents();

        $this->assertIsArray($subscribedEvents);
        $this->assertTrue(in_array(Events::prePersist, $subscribedEvents));
        $this->assertTrue(in_array(Events::preUpdate, $subscribedEvents));
    }

    public function testHashPass(): void
    {
        $user = (new User())->setPassword('Test123-');

        $lifecycleEventArgs = new LifecycleEventArgs($user, $this->objectManager);
        $this->encoder->expects($this->once())->method('encodePassword')->willReturn('hashedPassword');

        $this->subscriber->prePersist($lifecycleEventArgs);
    }

    public function testPersistNotUserEntity(): void
    {
        $lifecycleEventArgs = new LifecycleEventArgs(new stdClass(), $this->objectManager);
        $this->encoder->expects($this->never())->method('encodePassword');

        $this->subscriber->prePersist($lifecycleEventArgs);
    }

    public function testUnchangedPassword(): void
    {
        $this->preUpdateEventArgs->expects($this->once())->method(
            'getEntityChangeSet'
        )->willReturn(
            [
                    'email' => [
                        'old@mail.de',
                        'new@mail.de',
                    ],
                ]
        );

        $this->encoder->expects($this->never())->method('encodePassword');
        $this->subscriber->preUpdate($this->preUpdateEventArgs);
    }

    public function testUpdatePassword(): void
    {
        $user = (new User())->setPassword('Test');

        $this->preUpdateEventArgs
            ->expects($this->once())
            ->method('getEntityChangeSet')
            ->willReturn(['password' => ['Test']]);

        $this->preUpdateEventArgs
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($user);

        $this->encoder->expects($this->once())->method('encodePassword')->willReturn('hashedPass');

        $this->subscriber->preUpdate($this->preUpdateEventArgs);
    }

    protected function tearDown(): void
    {
        $this->encoder = null;
        $this->subscriber = null;
        $this->objectManager = null;
        $this->preUpdateEventArgs = null;
    }
}
