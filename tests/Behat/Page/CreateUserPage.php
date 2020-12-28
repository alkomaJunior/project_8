<?php
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

class CreateUserPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'user_create';
    }

    public function createUser(string $username, string $email, string $password, string $role): void
    {
        $this->open();
        $this->getDocument()->fillField('user_username', $username);
        $this->getDocument()->fillField('user_email', $email);
        $this->getDocument()->fillField('user_password_first', $password);
        $this->getDocument()->fillField('user_password_second', $password);
        $this->getDocument()->selectFieldOption('user_roles', $role);
        $this->getDocument()->pressButton('Ajouter');
    }
}