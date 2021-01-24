<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskVoter extends Voter
{
    public const DELETE = 'DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::DELETE])
            && $subject instanceof Task;
    }

    /**
     * @param string         $attribute
     * @param Task           $subject
     * @param TokenInterface $token
     *
     * @return bool|void
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $loggedUser = $token->getUser();

        if (!$loggedUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        if (self::DELETE === $attribute) {
            return $this->isDeletable($task, $loggedUser);
        }

        throw new LogicException('This code should not be reached!');
    }

    /**
     * Admin can delete anonymous Tasks.
     *
     * @param Task $task
     * @param User $loggedUser
     *
     * @return bool
     */
    private function isDeletable(Task $task, User $loggedUser): bool
    {
        $author = $task->getUser();

        return  ((!isset($author)) && $this->security->isGranted('ROLE_ADMIN')) || $loggedUser === $task->getUser();
    }
}
