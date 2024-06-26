<?php

namespace App\Entity;

use App\Repository\EtatSystemeRequeteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatSystemeRequeteRepository::class)]
class EtatSystemeRequete
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $etat = null;

    /**
     * @var Collection<int, Requetes>
     */
    #[ORM\OneToMany(targetEntity: Requetes::class, mappedBy: 'etat_systeme_requete')]
    private Collection $requetes;

    public function __construct()
    {
        $this->requetes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Requetes>
     */
    public function getRequetes(): Collection
    {
        return $this->requetes;
    }

    public function addRequete(Requetes $requete): static
    {
        if (!$this->requetes->contains($requete)) {
            $this->requetes->add($requete);
            $requete->setEtatSystemeRequete($this);
        }

        return $this;
    }

    public function removeRequete(Requetes $requete): static
    {
        if ($this->requetes->removeElement($requete)) {
            // set the owning side to null (unless already changed)
            if ($requete->getEtatSystemeRequete() === $this) {
                $requete->setEtatSystemeRequete(null);
            }
        }

        return $this;
    }
}
