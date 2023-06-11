<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 13)]
    private string $isbn;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $publishedDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bookDescription = null;

    #[ORM\Column(nullable: true)]
    private ?float $averageRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $ratingsCount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverUrl = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Subscribe::class)]
    private Collection $subscribers;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Comment::class)]
    private Collection $comments;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getBookDescription(): ?string
    {
        return $this->bookDescription;
    }

    public function setBookDescription(?string $bookDescription): self
    {
        $this->bookDescription = $bookDescription;

        return $this;
    }

    public function getPublishedDate(): ?\DateTime
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(?\DateTime $publishedDate): self
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

    public function getCoverUrl(): ?string
    {
        return $this->coverUrl;
    }

    public function setCoverUrl(?string $coverUrl): self
    {
        $this->coverUrl = $coverUrl;

        return $this;
    }

    public function isUserSubscribed(User $user): bool
    {
        return $this->subscribers->contains($user);
    }

    public function addSubscriber(User $user): self
    {
        if (!$this->subscribers->contains($user)) {
            $this->subscribers->add($user);
        }

        return $this;
    }

    public function removeSubscriber(User $user): self
    {
        if ($this->subscribers->contains($user)) {
            $this->subscribers->removeElement($user);
        }

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
            // set the owning side to null (unless already changed)
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }

        return $this;
    }
}
