<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\Behat\Context;

use App\Tests\Behat\Page\CreateUserPage;
use Behat\Behat\Context\Context;

class AdminContext implements Context
{
    private CreateUserPage $createUserPage;

    public function __construct(CreateUserPage $createUserPage)
    {
        $this->createUserPage = $createUserPage;
    }

    /**
     * @When /^I create user with username: "([^"]+)", email: "([^"]+)",password: "([^"]+)" and role: "([^"]+)"$/
     */
    public function iCreateUser(string $username, string $email, string $password, string $role): void
    {
        $this->createUserPage->createUser($username, $email, $password, $role);
    }
}
