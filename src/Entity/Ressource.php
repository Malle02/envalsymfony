<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RessourceRepository::class)]
#[Vich\Uploadable]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $presentation = null;

    #[ORM\Column(length: 50)]
    private ?string $support = null; // Correction de suppport à support

    #[ORM\Column(length: 50)]
    private ?string $nature = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    /**
     * @var Collection<int, Etape>
     */
    #[ORM\ManyToMany(targetEntity: Etape::class, mappedBy: 'ressources')]
    private Collection $etapes;

    #[Vich\UploadableField(mapping: 'ressource_files', fileNameProperty: 'url')]
    private ?File $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->etapes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): static
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getSupport(): ?string
    {
        return $this->support;
    }

    public function setSupport(string $support): static
    {
        $this->support = $support;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): static
    {
        $this->nature = $nature;

        return $this;
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

    /**
     * @return Collection<int, Etape>
     */
    public function getEtapes(): Collection
    {
        return $this->etapes;
    }

    public function addEtape(Etape $etape): static
    {
        if (!$this->etapes->contains($etape)) {
            $this->etapes->add($etape);
            $etape->addRessource($this);
        }

        return $this;
    }

    public function removeEtape(Etape $etape): static
    {
        if ($this->etapes->removeElement($etape)) {
            $etape->removeRessource($this);
        }

        return $this;
    }

    public function getFichier(): ?File
    {
        return $this->fichier;
    }

    public function setFichier(?File $fichier = null): static
    {
        $this->fichier = $fichier;
        
        if (null !== $fichier) {
            // Il est nécessaire qu'au moins un champ change si vous utilisez Doctrine,
            // sinon les écouteurs d'événements ne seront pas appelés et le fichier sera perdu
            $this->updatedAt = new \DateTime();
        }
        
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
}