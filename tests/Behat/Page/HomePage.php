<?php declare(strict_types=1);
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class HomePage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'homepage';
    }

    public function login(string $username, string $password): void
    {
        $this->open();
        $this->getDocument()->fillField('username', $username);
        $this->getDocument()->fillField('password', $password);
        $this->getDocument()->pressButton('Se connecter');
    }
}