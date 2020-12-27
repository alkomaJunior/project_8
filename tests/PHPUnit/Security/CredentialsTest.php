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

use App\Security\DataTransferObject\Credentials;
use App\Tests\PHPUnit\Helper\HelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CredentialsTest extends KernelTestCase
{
    use HelperTrait;

    /**
     * @var Credentials|null
     */
    private ?Credentials $credentials;

    protected function setUp(): void
    {
        $this->credentials = new Credentials();
    }

    public function testEntity(): void
    {
        $this->credentials->setUsername('username');
        $this->credentials->setPassword('Password123-');
        $this->credentials->setCsrfToken('Token123');

        $this->assertEquals('username', $this->credentials->getUsername());
        $this->assertEquals('Password123-', $this->credentials->getPassword());
        $this->assertEquals('Token123', $this->credentials->getCsrfToken());
    }

    protected function tearDown(): void
    {
        $this->credentials = null;
    }
}
