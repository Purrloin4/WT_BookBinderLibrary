<?php

namespace App\Entity;

use App\Repository\SubscribeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscribeRepository::class)]
class Subscribe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subscribes')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'subscribes')]
    #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id', nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(type: 'boolean')]
    private bool $status;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $timeStamp;

    public function __construct()
    {
        $this->timeStamp = new \DateTimeImmutable();
        $this->status = true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTimeStamp(): \DateTimeInterface
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(\DateTimeInterface $timeStamp): self
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }
}
