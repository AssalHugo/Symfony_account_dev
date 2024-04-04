<?php

namespace App\Entity;

use App\Repository\StockageMesuresHomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockageMesuresHomeRepository::class)]
class StockageMesuresHome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_mesure = null;

    #[ORM\Column]
    private ?int $valeur_use = null;

    #[ORM\Column]
    private ?int $valeur_max = null;

    #[ORM\ManyToMany(targetEntity: ResStockagesHome::class, mappedBy: 'mesures')]
    private Collection $resStockagesHomes;

    public function __construct()
    {
        $this->resStockagesHomes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMesure(): ?\DateTimeInterface
    {
        return $this->date_mesure;
    }

    public function setDateMesure(\DateTimeInterface $date_mesure): static
    {
        $this->date_mesure = $date_mesure;

        return $this;
    }

    public function getValeurUse(): ?int
    {
        return $this->valeur_use;
    }

    public function setValeurUse(int $valeur_use): static
    {
        $this->valeur_use = $valeur_use;

        return $this;
    }

    public function getValeurMax(): ?int
    {
        return $this->valeur_max;
    }

    public function setValeurMax(int $valeur_max): static
    {
        $this->valeur_max = $valeur_max;

        return $this;
    }

    /**
     * @return Collection<int, ResStockagesHome>
     */
    public function getResStockagesHomes(): Collection
    {
        return $this->resStockagesHomes;
    }

    public function addResStockagesHome(ResStockagesHome $resStockagesHome): static
    {
        if (!$this->resStockagesHomes->contains($resStockagesHome)) {
            $this->resStockagesHomes->add($resStockagesHome);
            $resStockagesHome->addMesure($this);
        }

        return $this;
    }

    public function removeResStockagesHome(ResStockagesHome $resStockagesHome): static
    {
        if ($this->resStockagesHomes->removeElement($resStockagesHome)) {
            $resStockagesHome->removeMesure($this);
        }

        return $this;
    }
}
