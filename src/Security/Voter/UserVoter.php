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

use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Manage security to edit user.
 * ROLE_ADMIN can update all users, ROLE_USER can only update their own account
 */
class UserVoter extends Voter
{
    public const EDIT = 'EDIT';

    private $security;

    /**
     * UserVoter constructor.
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
        return self::EDIT === $attribute && $subject instanceof User;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $editedUser, TokenInterface $token): bool
    {
        /** @var User $loggedUser */
        $loggedUser = $token->getUser();

        if (!$loggedUser instanceof User) {
            return false;
        }

        if (self::EDIT === $attribute) {
            return $loggedUser->isEqualTo($editedUser) || $this->security->isGranted(User::ROLE_ADMIN);
        }

        throw new LogicException('This code should not be reached!');
    }
}
