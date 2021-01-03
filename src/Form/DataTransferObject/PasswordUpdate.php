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

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

/**
 * Transfer data from form Password update to user.
 */
class PasswordUpdate implements PasswordInterface
{
    /**
     * @Assert\NotBlank(groups={"account"})
     *
     * @CustomAssert\MatchPassword(message="Ce n'est pas votre mot de passe actuel: {{ string }}")
     */
    private ?string $actualPassword;

    /**
     * @Assert\Length(
     *     min=8,
     *     minMessage="Votre mot de passe doit faire au moins {{ limit }} caractères !",
     *     allowEmptyString = false
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[a-z])(?=.*[A-Z])/",
     *     message="Votre mot de passe devrait contenir au moin une lettre en majuscule et en minuscule !"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[0-9])/",
     *     message="Votre mot de passe devrait contenir des nombres !"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[\W])/",
     *     message="Votre mot de passe devrait contenir au moin un caractère spécial!"
     * )
     */
    private ?string $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous avez entrer deux mots de passes diffèrents")
     */
    private ?string $confirmPassword;

    /**
     * {@inheritdoc}
     */
    public function getActualPassword(): ?string
    {
        return $this->actualPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setActualPassword(string $actualPassword): PasswordInterface
    {
        $this->actualPassword = $actualPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setNewPassword(string $newPassword): PasswordInterface
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmPassword(string $confirmPassword): PasswordInterface
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
