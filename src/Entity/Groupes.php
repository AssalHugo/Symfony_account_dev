<?php

namespace App\Entity;

use App\Repository\GroupesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupesRepository::class)]
class Groupes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $nom = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $acronyme = null;

    #[ORM\Column(length: 34)]
    private ?string $statut = null;

    #[ORM\OneToOne(inversedBy: 'reponsable_de', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employe $responsable = null;

    #[ORM\ManyToMany(targetEntity: Employe::class, inversedBy: 'adjoint_de')]
    private Collection $adjoints;

    #[ORM\ManyToMany(targetEntity: Employe::class, mappedBy: 'groupes_secondaires')]
    private Collection $employe_grp_secondaires;

    public function __construct()
    {
        $this->adjoints = new ArrayCollection();
        $this->employe_grp_secondaires = new ArrayCollection();
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

    public function getAcronyme(): ?string
    {
        return $this->acronyme;
    }

    public function setAcronyme(?string $acronyme): static
    {
        $this->acronyme = $acronyme;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getResponsable(): ?Employe
    {
        return $this->responsable;
    }

    public function setResponsable(Employe $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getAdjoints(): Collection
    {
        return $this->adjoints;
    }

    public function addAdjoint(Employe $adjoint): static
    {
        if (!$this->adjoints->contains($adjoint)) {
            $this->adjoints->add($adjoint);
        }

        return $this;
    }

    public function removeAdjoint(Employe $adjoint): static
    {
        $this->adjoints->removeElement($adjoint);

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployeGrpSecondaires(): Collection
    {
        return $this->employe_grp_secondaires;
    }

    public function addEmployeGrpSecondaire(Employe $employeGrpSecondaire): static
    {
        if (!$this->employe_grp_secondaires->contains($employeGrpSecondaire)) {
            $this->employe_grp_secondaires->add($employeGrpSecondaire);
            $employeGrpSecondaire->addGroupesSecondaire($this);
        }

        return $this;
    }

    public function removeEmployeGrpSecondaire(Employe $employeGrpSecondaire): static
    {
        if ($this->employe_grp_secondaires->removeElement($employeGrpSecondaire)) {
            $employeGrpSecondaire->removeGroupesSecondaire($this);
        }

        return $this;
    }
}
