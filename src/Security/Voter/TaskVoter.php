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

/**
 * Manage security to delete task.
 * ROLE_ADMIN can delete Anonymous Tasks, and only author can delete their own tasks.
 */
class TaskVoter extends Voter
{
    public const DELETE = 'DELETE';

    private $security;

    /**
     * TaskVoter constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return self::DELETE === $attribute && $subject instanceof Task;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $task, TokenInterface $token): bool
    {
        /** @var User $user */
        $loggedUser = $token->getUser();
        $author = $task->getUser();

        if (!$loggedUser instanceof User) {
            return false;
        }

        if (self::DELETE === $attribute) {
            return ((!isset($author)) && $this->security->isGranted(User::ROLE_ADMIN))
                || $loggedUser->isEqualTo($author);
        }

        throw new LogicException('This code should not be reached!');
    }
}
