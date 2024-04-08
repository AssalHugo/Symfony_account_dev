<?php

namespace App\Entity;

use App\Repository\StockagesMesuresWorkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockagesMesuresWorkRepository::class)]
class StockagesMesuresWork
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

    #[ORM\ManyToOne(inversedBy: 'mesure')]
    private ?ResStockageWork $resStockageWork = null;

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

    public function getResStockageWork(): ?ResStockageWork
    {
        return $this->resStockageWork;
    }

    public function setResStockageWork(?ResStockageWork $resStockageWork): static
    {
        $this->resStockageWork = $resStockageWork;

        return $this;
    }
}
