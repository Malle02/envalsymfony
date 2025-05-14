<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?\DateTime $dateHeure = null;

    /**
     * @var Collection<int, Rendu>
     */
    #[ORM\OneToMany(targetEntity: Rendu::class, mappedBy: 'message')]
    private Collection $rendus;

    #[ORM\ManyToOne(inversedBy: 'messagesSent')]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'messagesReceived')]
    private ?User $receiver = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'messages')]
    private ?self $replyTo = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'replyTo')]
    private Collection $messages;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'replyTo')]
    private Collection $replies;

    public function __construct()
    {
        $this->rendus = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->replies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateHeure(): ?\DateTime
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTime $dateHeure): static
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    /**
     * @return Collection<int, Rendu>
     */
    public function getRendus(): Collection
    {
        return $this->rendus;
    }

    public function addRendu(Rendu $rendu): static
    {
        if (!$this->rendus->contains($rendu)) {
            $this->rendus->add($rendu);
            $rendu->setMessage($this);
        }

        return $this;
    }

    public function removeRendu(Rendu $rendu): static
    {
        if ($this->rendus->removeElement($rendu)) {
            // set the owning side to null (unless already changed)
            if ($rendu->getMessage() === $this) {
                $rendu->setMessage(null);
            }
        }

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getReplyTo(): ?self
    {
        return $this->replyTo;
    }

    public function setReplyTo(?self $replyTo): static
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(self $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setReplyTo($this);
        }

        return $this;
    }

    public function removeMessage(self $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getReplyTo() === $this) {
                $message->setReplyTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setReplyTo($this);
        }

        return $this;
    }

    public function removeReply(self $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getReplyTo() === $this) {
                $reply->setReplyTo(null);
            }
        }

        return $this;
    }
}
