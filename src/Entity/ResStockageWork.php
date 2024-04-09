<?php

namespace App\Entity;

use App\Repository\ResStockageWorkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResStockageWorkRepository::class)]
class ResStockageWork
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
    #[ORM\ManyToMany(targetEntity: Employe::class, inversedBy: 'resStockageWorks')]
    private Collection $responsables;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?GroupesSys $groupeSys = null;

    /**
     * @var Collection<int, StockagesMesuresWork>
     */
    #[ORM\OneToMany(targetEntity: StockagesMesuresWork::class, mappedBy: 'resStockageWork')]
    private Collection $mesure;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $path;

    public function __construct()
    {
        $this->responsables = new ArrayCollection();
        $this->mesure = new ArrayCollection();
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
    public function getResponsables(): Collection
    {
        return $this->responsables;
    }

    public function addResponsable(Employe $responsable): static
    {
        if (!$this->responsables->contains($responsable)) {
            $this->responsables->add($responsable);
        }

        return $this;
    }

    public function removeResponsable(Employe $responsable): static
    {
        $this->responsables->removeElement($responsable);

        return $this;
    }

    public function getGroupeSys(): ?GroupesSys
    {
        return $this->groupeSys;
    }

    public function setGroupeSys(?GroupesSys $groupeSys): static
    {
        $this->groupeSys = $groupeSys;

        return $this;
    }

    /**
     * @return Collection<int, StockagesMesuresWork>
     */
    public function getMesures(): Collection
    {
        return $this->mesure;
    }

    public function addMesure(StockagesMesuresWork $mesure): static
    {
        if (!$this->mesure->contains($mesure)) {
            $this->mesure->add($mesure);
            $mesure->setResStockageWork($this);
        }

        return $this;
    }

    public function removeMesure(StockagesMesuresWork $mesure): static
    {
        if ($this->mesure->removeElement($mesure)) {
            // set the owning side to null (unless already changed)
            if ($mesure->getResStockageWork() === $this) {
                $mesure->setResStockageWork(null);
            }
        }

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }
}
