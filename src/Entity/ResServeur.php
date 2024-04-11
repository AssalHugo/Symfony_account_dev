<?php

namespace App\Entity;

use App\Repository\ResServeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResServeurRepository::class)]
class ResServeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Employe>
     */
    #[ORM\ManyToMany(targetEntity: Employe::class, inversedBy: 'resServeurs')]
    private Collection $responsable;

    #[ORM\ManyToOne(inversedBy: 'resServeurs')]
    private ?Groupes $groupe = null;

    /**
     * @var Collection<int, ServeursMesures>
     */
    #[ORM\OneToMany(targetEntity: ServeursMesures::class, mappedBy: 'resServeur')]
    private Collection $serveursMesures;

    public function __construct()
    {
        $this->responsable = new ArrayCollection();
        $this->serveursMesures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getResponsable(): Collection
    {
        return $this->responsable;
    }

    public function addResponsable(Employe $user): static
    {
        if (!$this->responsable->contains($user)) {
            $this->responsable->add($user);
        }

        return $this;
    }

    public function removeResponsable(Employe $user): static
    {
        $this->responsable->removeElement($user);

        return $this;
    }

    public function getGroupe(): ?Groupes
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupes $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * @return Collection<int, ServeursMesures>
     */
    public function getMesures(): Collection
    {
        return $this->serveursMesures;
    }

    public function addMesure(ServeursMesures $serveursMesure): static
    {
        if (!$this->serveursMesures->contains($serveursMesure)) {
            $this->serveursMesures->add($serveursMesure);
            $serveursMesure->setResServeur($this);
        }

        return $this;
    }

    public function removeServeursMesure(ServeursMesures $serveursMesure): static
    {
        if ($this->serveursMesures->removeElement($serveursMesure)) {
            // set the owning side to null (unless already changed)
            if ($serveursMesure->getResServeur() === $this) {
                $serveursMesure->setResServeur(null);
            }
        }

        return $this;
    }
}
