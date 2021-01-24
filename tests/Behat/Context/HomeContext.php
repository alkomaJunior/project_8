<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

declare(strict_types=1);

namespace App\Tests\Behat\Context;

use App\Tests\Behat\Page\HomePage;
use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;

class HomeContext implements Context
{
    private HomePage $loginPage;

    public function __construct(HomePage $loginPage)
    {
        $this->loginPage = $loginPage;
    }

    /**
     * @Given I am on the login page
     * @throws UnexpectedPageException
     */
    public function iAmOnTheLoginPage(): void
    {
        $this->loginPage->open();
    }
    /**
     * @When /^I connect as "([^"]+)" with the password "([^"]+)"$/
     */
    public function connexion(string $username, string $password): void
    {
        $this->loginPage->login($username, $password);
    }
}