<?php

namespace App\Entity;

use App\Repository\LocalisationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalisationsRepository::class)]
class Localisations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $bureau = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Batiments $batiment = null;

    #[ORM\ManyToMany(targetEntity: Employe::class, mappedBy: 'localisation')]
    private Collection $employes;

    #[ORM\OneToOne(mappedBy: 'localisation', cascade: ['persist', 'remove'])]
    private ?Requetes $requetes = null;

    public function __construct()
    {
        $this->employes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBureau(): ?string
    {
        return $this->bureau;
    }

    public function setBureau(string $bureau): static
    {
        $this->bureau = $bureau;

        return $this;
    }

    public function getBatiment(): ?Batiments
    {
        return $this->batiment;
    }

    public function setBatiment(?Batiments $batiment): static
    {
        $this->batiment = $batiment;

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(Employe $employe): static
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->addLocalisation($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): static
    {
        if ($this->employes->removeElement($employe)) {
            $employe->removeLocalisation($this);
        }

        return $this;
    }

    public function getRequetes(): ?Requetes
    {
        return $this->requetes;
    }

    public function setRequetes(?Requetes $requetes): static
    {
        // unset the owning side of the relation if necessary
        if ($requetes === null && $this->requetes !== null) {
            $this->requetes->setLocalisation(null);
        }

        // set the owning side of the relation if necessary
        if ($requetes !== null && $requetes->getLocalisation() !== $this) {
            $requetes->setLocalisation($this);
        }

        $this->requetes = $requetes;

        return $this;
    }
}
