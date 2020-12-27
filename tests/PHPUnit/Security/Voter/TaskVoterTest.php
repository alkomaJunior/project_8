<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

class TaskVoterTest extends TestCase
{
    private ?TokenInterface $token;
    private ?Security $security;
    private ?TaskVoter $voter;

    // Logged user with default role: "ROLE_USER"
    private ?User $loggedUser;

    // Anonymous Task
    private ?Task $task;

    protected function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->voter = new TaskVoter($this->security);
        $this->task = (new Task())->setUser(null);
        $this->loggedUser = new User();
    }

    public function testDeniedDeleteTaskFromAnonymous(): void
    {
        $this->token->expects($this->once())->method('getUser')->willReturn(null);

        $result = $this->voter->vote($this->token, $this->task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeleteTaskFromAuthor(): void
    {
        $this->token->expects($this->once())->method('getUser')->willReturn(
            $this->loggedUser
        );

        $this->security->method('isGranted')->with(User::ROLE_USER)->willReturn(true);

        $this->task->setUser($this->loggedUser);

        $result = $this->voter->vote($this->token, $this->task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testDeleteAnonymousTaskFromAdmin(): void
    {
        $this->loggedUser->setRoles([User::ROLE_ADMIN]);

        $this->token->expects($this->once())->method('getUser')->willReturn(
            $this->loggedUser
        );

        $this->security->method('isGranted')->with(User::ROLE_ADMIN)->willReturn(true);

        $result = $this->voter->vote($this->token, $this->task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testDeniedDeleteAnonymousTaskFromUser(): void
    {
        $this->token->expects($this->once())->method('getUser')->willReturn(
            $this->loggedUser
        );

        $this->security->method('isGranted')->with(User::ROLE_ADMIN)->willReturn(false);

        $result = $this->voter->vote($this->token, $this->task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniedDeleteKnownAuthorTask(): void
    {
        $this->loggedUser->setRoles([User::ROLE_ADMIN]);

        $this->token->expects($this->once())->method('getUser')->willReturn(
            $this->loggedUser
        );

        $this->security->method('isGranted')->with(User::ROLE_ADMIN)->willReturn(true);

        $this->task->setUser(new User());

        $result = $this->voter->vote($this->token, $this->task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeleteTaskWithWrongAttribute(): void
    {
        $this->loggedUser->setRoles([User::ROLE_ADMIN]);

        $this->token->expects($this->never())->method('getUser');

        $this->security->expects($this->never())->method('isGranted');
        $result = $this->voter->vote($this->token, $this->task, ['WRONG_ATTRIBUTE']);

        $this->assertEquals(0, $result);
    }

    protected function tearDown(): void
    {
        $this->security = null;
        $this->token = null;
        $this->voter = null;
        $this->loggedUser = null;
        $this->token = null;
    }
}
