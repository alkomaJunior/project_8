<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Form\DataTransferObject;

/**
 * Represents the interface that all FormPassword modify must implement.
 *
 * This interface is useful because by changing the account password, the actual
 * password from logged user will be also changed, and the validation will
 * have conflict and see the password as unchanged.
 *
 * @author Fabien Potencier <bigboss@it-bigboss.de>
 */
interface PasswordInterface
{
    /**
     * @return string
     */
    public function getActualPassword(): string;

    /**
     * @param string $actualPassword
     *
     * @return PasswordInterface
     */
    public function setActualPassword(string $actualPassword): self;

    /**
     * @return string
     */
    public function getNewPassword(): string;

    /**
     * @param string $newPassword
     *
     * @return PasswordInterface
     */
    public function setNewPassword(string $newPassword): self;

    /**
     * @return string
     */
    public function getConfirmPassword(): string;

    /**
     * @param string $confirmPassword
     *
     * @return PasswordInterface
     */
    public function setConfirmPassword(string $confirmPassword): self;
}
