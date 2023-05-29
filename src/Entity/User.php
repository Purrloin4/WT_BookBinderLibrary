<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 60)]
    private ?string $display_name = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Friendship::class, orphanRemoval: true)]
    private Collection $sentFriendships;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Friendship::class, orphanRemoval: true)]
    private Collection $receivedFriendship;

    #[ORM\OneToMany(mappedBy: 'Sender', targetEntity: Message::class)]
    private Collection $sendMessages;

    #[ORM\OneToMany(mappedBy: 'Receiver', targetEntity: Message::class)]
    private Collection $receivedMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Follow::class)]
    private Collection $follows;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->sentFriendships = new ArrayCollection();
        $this->receivedFriendship = new ArrayCollection();
        $this->sendMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->follows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name): self
    {
        $this->display_name = $display_name;

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendships(): Collection
    {
        return new ArrayCollection(array_merge(
            $this->sentFriendships->toArray(),
            $this->receivedFriendship->toArray()));
    }

    public function addFriendship(Friendship $friendship): self // redundant gives warning so maybe remove @Sinyeol
    {
        if (!$this->sentFriendships->contains($friendship)) {
            $this->friendships->add($friendship);
            $friendship->setSender($this);
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship): self
    {
        // the friendship should be removed from the entity manager
        if ($friendship->getSender() === $this) {
            $this->sentFriendships->removeElement($friendship);
        } else {
            $this->receivedFriendship->removeElement($friendship);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSendMessages(): Collection
    {
        return $this->sendMessages;
    }

    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addSendMessage(Message $message): self
    {
        if (!$this->sendMessages->contains($message)) {
            $this->sendMessages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function addReceivedMessage(Message $message): self
    {
        if (!$this->receivedMessages->contains($message)) {
            $this->receivedMessages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->sendMessages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setMessage('This message was removed by: '.$this->display_name);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(Follow $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows->add($follow);
            $follow->setUser($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): self
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getUser() === $this) {
                $follow->setUser(null);
            }
        }

        return $this;
    }
}
