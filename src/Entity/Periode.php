<?php

namespace App\Entity;

use App\Repository\PeriodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodeRepository::class)]
class Periode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private ?string $type = null;

    /**
     * @var Collection<int, StockagesMesuresWork>
     */
    #[ORM\OneToMany(targetEntity: StockagesMesuresWork::class, mappedBy: 'periode')]
    private Collection $stockagesMesuresWorks;

    /**
     * @var Collection<int, StockagesMesuresHome>
     */
    #[ORM\OneToMany(targetEntity: StockagesMesuresHome::class, mappedBy: 'periode')]
    private Collection $stockageMesuresHome;

    public function __construct()
    {
        $this->stockagesMesuresWorks = new ArrayCollection();
        $this->stockageMesuresHome = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, StockagesMesuresWork>
     */
    public function getStockagesMesuresWorks(): Collection
    {
        return $this->stockagesMesuresWorks;
    }

    public function addStockagesMesuresWork(StockagesMesuresWork $stockagesMesuresWork): static
    {
        if (!$this->stockagesMesuresWorks->contains($stockagesMesuresWork)) {
            $this->stockagesMesuresWorks->add($stockagesMesuresWork);
            $stockagesMesuresWork->setPeriode($this);
        }

        return $this;
    }

    public function removeStockagesMesuresWork(StockagesMesuresWork $stockagesMesuresWork): static
    {
        if ($this->stockagesMesuresWorks->removeElement($stockagesMesuresWork)) {
            // set the owning side to null (unless already changed)
            if ($stockagesMesuresWork->getPeriode() === $this) {
                $stockagesMesuresWork->setPeriode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StockagesMesuresHome>
     */
    public function getStockageMesuresHome(): Collection
    {
        return $this->stockageMesuresHome;
    }

    public function addStockageMesuresHome(StockagesMesuresHome $stockageMesuresHome): static
    {
        if (!$this->stockageMesuresHome->contains($stockageMesuresHome)) {
            $this->stockageMesuresHome->add($stockageMesuresHome);
            $stockageMesuresHome->setPeriode($this);
        }

        return $this;
    }

    public function removeStockageMesuresHome(StockagesMesuresHome $stockageMesuresHome): static
    {
        if ($this->stockageMesuresHome->removeElement($stockageMesuresHome)) {
            // set the owning side to null (unless already changed)
            if ($stockageMesuresHome->getPeriode() === $this) {
                $stockageMesuresHome->setPeriode(null);
            }
        }

        return $this;
    }
}
