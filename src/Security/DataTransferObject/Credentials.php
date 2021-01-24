<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Security\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Credentials.
 */
class Credentials
{
    /**
     * @Assert\NotBlank
     */
    private ?string $username = null;

    /**
     * @Assert\NotBlank
     */
    private ?string $password = null;

    /**
     * @Assert\NotBlank
     */
    private ?string $csrfToken = null;

    /**
     * Credentials constructor.
     *
     * @param string|null $username
     * @param string|null $password
     * @param string|null $csrfToken
     */
    public function __construct(?string $username = null, ?string $password = null, ?string $csrfToken = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->csrfToken = $csrfToken;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getCsrfToken(): ?string
    {
        return $this->csrfToken;
    }

    public function setCsrfToken(?string $csrfToken): void
    {
        $this->csrfToken = $csrfToken;
    }
}
