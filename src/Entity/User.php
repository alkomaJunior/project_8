<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("user")
 * @ORM\Entity
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Un autre utilisateur s'est déjà inscrit avec cette email, merci de la modifier"
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Un autre utilisateur s'est déjà inscrit avec cet username, merci de le modifier"
 * )
 */
class User implements UserInterface, EquatableInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const DEFAULT_ROLES = [self::ROLE_USER];
    public const ALL_ROLES = [self::ROLE_ADMIN, self::ROLE_USER];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur!")
     * @Assert\Length(
     *      min = 2,
     *      max = 25,
     *      minMessage = "Votre nom d'utilisateur doit comporter au moins {{ limit }} caractères!",
     *      maxMessage = "Votre nom d'utilisateur ne peut pas comporter plus de {{ limit }} caractères!",
     *      allowEmptyString = false
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]/",
     *     message="Votre nom d'utilisateur devrait commencer par une lettre!"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9-]*$/",
     *     message="Votre nom d'utilisateur devrait contenir seulement des lettres, des chiffres et le caractère '-'!"
     * )
     */
    private string $username = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Vous devez saisir un mot de passe!")
     * @Assert\Length(
     *     min=8,
     *     minMessage="Votre mot de passe doit faire au moins {{ limit }} caractères!"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[a-z])(?=.*[A-Z])/",
     *     message="Votre mot de passe devrait contenir au moin une lettre en majuscule et en minuscule!"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[0-9])/",
     *     message="Votre mot de passe devrait contenir des nombres!"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[\W])/",
     *     message="Votre mot de passe devrait contenir au moin un caractère spécial!"
     * )
     */
    private string $password = '';

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Vous devez saisir une adresse email!")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte!")
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Votre adresse e-mail ne peut pas comporter plus de {{ limit }} caractères!",
     *      allowEmptyString = false
     * )
     */
    private string $email = '';

    /**
     * @ORM\Column(type="json")
     *
     * @Assert\Choice({{User::ROLE_ADMIN},{User::ROLE_USER}}, message="Choisissez un rôle valide!")
     *
     * @var string[]
     */
    private array $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user")
     *
     * @var Collection<int, Task>
     */
    protected Collection $tasks;

    /**
     * Initialize User Object with ROLE_USER as default role.
     */
    public function __construct()
    {
        $this->roles = self::DEFAULT_ROLES;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function eraseCredentials(): void
    {
        // Do nothing because no sensitive information is stored .
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(?UserInterface $user): bool
    {
        if (null === $user) {
            return false;
        }

        return $user->getUsername() === $this->getUsername();
    }
}
