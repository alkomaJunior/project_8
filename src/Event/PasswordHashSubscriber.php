<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Event;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordHashSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->hashPassword($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        // end the event if the password doesn't change
        if (!array_key_exists('password', $args->getEntityChangeSet())) {
            return;
        }

        $this->hashPassword($args->getObject());
    }

    /**
     * @param object $entity
     */
    private function hashPassword(object $entity): void
    {
        if (!$entity instanceof User) {
            return;
        }

        $entity->setPassword($this->encoder->encodePassword($entity, $entity->getPassword()));
    }
}
