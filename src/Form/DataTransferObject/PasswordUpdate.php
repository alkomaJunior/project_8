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

class PasswordUpdate
{
    /**
     * @Assert\NotBlank(groups={"account"})
     * @CustomAssert\MatchPassword(message="Ce n'est pas votre mot de passe actuel: {{ string }}")
     */
    private $actualPassword;

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
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous avez entrer deux mots de passes diffèrents")
     */
    private $confirmPassword;

    public function getActualPassword(): ?string
    {
        return $this->actualPassword;
    }

    public function setActualPassword(string $actualPassword): self
    {
        $this->actualPassword = $actualPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
