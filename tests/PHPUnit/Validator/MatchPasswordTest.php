<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Entity;

use App\Entity\User;
use App\Validator\MatchPassword;
use App\Validator\MatchPasswordValidator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class MatchPasswordTest extends KernelTestCase
{
    use FixturesTrait;

    const NEW_PASSWORD = '*TestPassword1';

    private ?User $user;
    private ?Security $security;
    private ?MatchPasswordValidator $matchPasswordValidator;
    private ?ExecutionContextInterface $executionContextMock;
    private ?ConstraintViolationBuilderInterface $constraintViolationBuilderMock;

    protected function setUp(): void
    {
        self::bootKernel();
        $fixturesUsers = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/users.yaml',
        ]);
        $this->user = $fixturesUsers['user_1'];
        $this->security = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->matchPasswordValidator = new MatchPasswordValidator($this->security);

        $this->executionContextMock = $this->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->constraintViolationBuilderMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->constraintMock = $this->getMockBuilder(MatchPassword::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testMatchNotValidPassword(): void
    {
        $value = 'incorrectPassword';
        $this->security->expects($this->once())->method('getUser')->willReturn($this->user);

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->with('This is not your actual password: "{{ string }}" !')
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ string }}', $value)
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects($this->once())
            ->method('addViolation')
            ->willReturn($this->constraintViolationBuilderMock);

        $this->matchPasswordValidator->initialize($this->executionContextMock);
        $this->matchPasswordValidator->validate($value, $this->constraintMock);
    }

    public function testMatchPasswordThrowUnexpectedValueException(): void
    {
        $value = 5;
        $this->security->expects($this->never())->method('getUser');

        $this->expectException(UnexpectedValueException::class);

        $this->matchPasswordValidator->initialize($this->executionContextMock);
        $this->matchPasswordValidator->validate($value, $this->constraintMock);
    }

    public function testMatchEmptyPassword(): void
    {
        $value = '';
        $this->security->expects($this->never())->method('getUser');

        $this->matchPasswordValidator->initialize($this->executionContextMock);
        $this->matchPasswordValidator->validate($value, $this->constraintMock);
    }

    public function testMatchPasswordValid(): void
    {
        //Retrieve password value from file tests/fixtures/users.yaml
        $value = 'test1';

        $this->security->expects($this->once())->method('getUser')->willReturn($this->user);

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->constraintViolationBuilderMock
            ->expects($this->never())
            ->method('setParameter');

        $this->constraintViolationBuilderMock
            ->expects($this->never())
            ->method('addViolation');

        $this->matchPasswordValidator->initialize($this->executionContextMock);
        $this->matchPasswordValidator->validate($value, $this->constraintMock);
    }

    protected function tearDown(): void
    {
        unset($this->user);
        unset($this->security);
        unset($this->matchPasswordValidator);
        unset($this->executionContextMock);
        unset($this->constraintViolationBuilderMock);
    }
}
