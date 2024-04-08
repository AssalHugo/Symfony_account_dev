<?php

namespace App\Entity;

use App\Repository\GroupesSysRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupesSysRepository::class)]
class GroupesSys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'groupesSys')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupes $groupe = null;


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

    public function getGroupe(): ?Groupes
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupes $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }
}
