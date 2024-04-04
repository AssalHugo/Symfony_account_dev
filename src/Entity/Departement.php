<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $nom = null;

    #[ORM\Column(length: 8)]
    private ?string $acronyme = null;

    //#[ORM\ManyToOne(targetEntity: Employe::class)]
    //#[ORM\JoinColumn(nullable: true)]
    //private ?Employe $responsable = null;

    #[ORM\OneToMany(targetEntity: Groupes::class, mappedBy: 'departement')]
    private Collection $groupes;

    #[ORM\ManyToOne(inversedBy: 'responsable_des_departements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employe $responsable = null;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
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

    public function setAcronyme(string $acronyme): static
    {
        $this->acronyme = $acronyme;

        return $this;
    }

    public function getResponsable(): ?Employe
    {
        return $this->responsable;
    }

    public function setResponsable(?Employe $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection<int, Groupes>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupes $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setDepartement($this);
        }

        return $this;
    }

    public function removeGroupe(Groupes $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getDepartement() === $this) {
                $groupe->setDepartement(null);
            }
        }

        return $this;
    }
}
