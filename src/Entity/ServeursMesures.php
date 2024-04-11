<?php

namespace App\Entity;

use App\Repository\ServeursMesuresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServeursMesuresRepository::class)]
class ServeursMesures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_mesure = null;

    #[ORM\Column]
    private ?int $cpu = null;

    #[ORM\Column]
    private ?int $cpu_total = null;

    #[ORM\Column]
    private ?int $ram_utilise = null;

    #[ORM\Column]
    private ?int $ram_max = null;

    #[ORM\Column]
    private ?int $nb_utilisateurs = null;

    #[ORM\ManyToOne(inversedBy: 'serveursMesures')]
    private ?ResServeur $resServeur = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMesure(): ?\DateTimeImmutable
    {
        return $this->date_mesure;
    }

    public function setDateMesure(\DateTimeImmutable $date_mesure): static
    {
        $this->date_mesure = $date_mesure;

        return $this;
    }

    public function getCpu(): ?int
    {
        return $this->cpu;
    }

    public function setCpu(int $cpu): static
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getRamUtilise(): ?int
    {
        return $this->ram_utilise;
    }

    public function setRamUtilise(int $ram_utilise): static
    {
        $this->ram_utilise = $ram_utilise;

        return $this;
    }

    public function getRamMax(): ?int
    {
        return $this->ram_max;
    }

    public function setRamMax(int $ram_max): static
    {
        $this->ram_max = $ram_max;

        return $this;
    }

    public function getNbUtilisateurs(): ?int
    {
        return $this->nb_utilisateurs;
    }

    public function setNbUtilisateurs(int $nb_utilisateurs): static
    {
        $this->nb_utilisateurs = $nb_utilisateurs;

        return $this;
    }

    public function getResServeur(): ?ResServeur
    {
        return $this->resServeur;
    }

    public function setResServeur(?ResServeur $resServeur): static
    {
        $this->resServeur = $resServeur;

        return $this;
    }

    public function getCpuTotal(): ?int
    {
        return $this->cpu_total;
    }

    public function setCpuTotal(int $cpu_total): static
    {
        $this->cpu_total = $cpu_total;

        return $this;
    }
}
