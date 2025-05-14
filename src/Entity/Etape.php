<?php

namespace App\Entity;

use App\Repository\EtapeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtapeRepository::class)]
class Etape
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consignes = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'etapes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parcours $parcours = null;

    /**
     * @var Collection<int, Rendu>
     */
    #[ORM\OneToMany(targetEntity: Rendu::class, mappedBy: 'etape')]
    private Collection $rendus;

    /**
     * @var Collection<int, Ressource>
     */
    #[ORM\ManyToMany(targetEntity: Ressource::class, inversedBy: 'etapes')]
    private Collection $ressources;

    public function __construct()
    {
        $this->rendus = new ArrayCollection();
        $this->ressources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getConsignes(): ?string
    {
        return $this->consignes;
    }

    public function setConsignes(?string $consignes): static
    {
        $this->consignes = $consignes;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getParcours(): ?Parcours
    {
        return $this->parcours;
    }

    public function setParcours(?Parcours $parcours): static
    {
        $this->parcours = $parcours;

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
            $rendu->setEtape($this);
        }

        return $this;
    }

    public function removeRendu(Rendu $rendu): static
    {
        if ($this->rendus->removeElement($rendu)) {
            // set the owning side to null (unless already changed)
            if ($rendu->getEtape() === $this) {
                $rendu->setEtape(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): static
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources->add($ressource);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): static
    {
        $this->ressources->removeElement($ressource);

        return $this;
    }
}
