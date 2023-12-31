<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $isbn = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedDate = null;

    #[ORM\Column(nullable: true)]
    private ?float $averageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $ratingsCount = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return Collection<>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBook($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeImmutable
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(\DateTimeImmutable $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    public function setAverageRating(?float $averageRating): self
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    public function getRatingsCount(): ?int
    {
        return $this->ratingsCount;
    }

    public function setRatingsCount(?int $ratingsCount): self
    {
        $this->ratingsCount = $ratingsCount;

        return $this;
    }

    public function addRating(int $rating): self
    {
        ++$this->ratingsCount;
        $this->averageRating = ($this->averageRating * ($this->ratingsCount - 1) + $rating) / $this->ratingsCount;

        return $this;
    }

    public function removeRating(int $rating): self
    {
        --$this->ratingsCount;
        $this->averageRating = ($this->averageRating * ($this->ratingsCount + 1) - $rating) / $this->ratingsCount;

        return $this;
    }
}
