<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Security\Voter;

use App\Entity\User;
use App\Security\Voter\UserVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

class UserVoterTest extends TestCase
{
    private ?TokenInterface $token;
    private ?Security $security;
    private ?UserVoter $voter;

    // Logged user with role: "ROLE_ADMIN"
    private ?User $loggedUser;
    // User will be edited
    private ?User $editedUser;

    protected function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->voter = new UserVoter($this->security);
        $this->editedUser = new User();
        $this->loggedUser = (new User())->setRoles([User::ROLE_ADMIN]);
    }

    public function testDeniedEditUserFromAnonymous(): void
    {
        $this->token->expects($this->once())->method('getUser')->willReturn(null);

        $result = $this->voter->vote($this->token, $this->editedUser, [UserVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testEditUserFromRoleAdmin(): void
    {
        $this->token->expects($this->once())->method('getUser')->willReturn(
            $this->loggedUser
        );

        $this->security->method('isGranted')->with(User::ROLE_ADMIN)->willReturn(true);

        $this->editedUser;

        $result = $this->voter->vote($this->token, $this->editedUser, [UserVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testDeniedEditUserFromRoleUser(): void
    {
        $this->loggedUser->setRoles([User::ROLE_USER]);

        $this->token->expects($this->once())->method('getUser')->willReturn(
            $this->loggedUser
        );

        $this->security->method('isGranted')->with(User::ROLE_ADMIN)->willReturn(false);

        $this->editedUser;

        $result = $this->voter->vote($this->token, $this->editedUser, [UserVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeleteTaskWithWrongAttribute(): void
    {
        $this->token->expects($this->never())->method('getUser');

        $this->security->expects($this->never())->method('isGranted');

        $result = $this->voter->vote($this->token, $this->editedUser, ['WRONG_ATTRIBUTE']);

        $this->assertEquals(0, $result);
    }

    protected function tearDown(): void
    {
        $this->security = null;
        $this->token = null;
        $this->editedUser = null;
        $this->loggedUser = null;
        $this->voter = null;
    }
}
