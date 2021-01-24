<?php

namespace App\Entity;

use Datetime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Task
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $createdAt;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Vous devez saisir un titre.")
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous devez saisir du contenu.")
     */
    private ?string $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isDone;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     */
    private ?User $user;

    public function __construct()
    {
        $this->isDone = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    /**
     * Return isDone value as String for url.
     */
    public function urlIsDoneValue(): string
    {
        return ($this->isDone) ? 'true' : 'false';
    }

    public function toggle(bool $flag): void
    {
        $this->isDone = $flag;
    }
}
