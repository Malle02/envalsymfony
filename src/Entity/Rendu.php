<?php

namespace App\Entity;

use App\Repository\RenduRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RenduRepository::class)]
#[Vich\Uploadable]


class Rendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeure = null;

    #[ORM\ManyToOne(inversedBy: 'rendus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etape $etape = null;

    #[ORM\ManyToOne(inversedBy: 'rendus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'rendus')]
    private ?Message $message = null;

    #[Vich\UploadableField(mapping: 'rendu_files', fileNameProperty: 'url')]
    private ?File $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTimeInterface $dateHeure): static
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    public function getEtape(): ?Etape
    {
        return $this->etape;
    }

    public function setEtape(?Etape $etape): static
    {
        $this->etape = $etape;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getFichier(): ?File
    {
        return $this->fichier;
    }

    public function setFichier(?File $fichier = null): self
    {
        $this->fichier = $fichier;
        
        if (null !== $fichier) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
}