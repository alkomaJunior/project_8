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
 * Manage security to edit user
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
     * @param $attribute
     * @param $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof User;
    }

    /**
     * @param string         $attribute
     * @param User           $subject
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

        /** @var User $user */
        $user = $subject;

        if (self::EDIT === $attribute) {
            return $this->isEditable($user, $loggedUser);
        }

        throw new LogicException('This code should not be reached!');
    }

    /**
     * Admin can delete anonymous Tasks.
     *
     * @param User $user
     * @param User $loggedUser
     *
     * @return bool
     */
    private function isEditable(User $user, User $loggedUser): bool
    {
        return $loggedUser === $user || $this->security->isGranted('ROLE_ADMIN');
    }
}
