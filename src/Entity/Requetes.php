<?php

namespace App\Entity;

use App\Repository\RequetesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequetesRepository::class)]
class Requetes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $nom = null;

    #[ORM\Column(length: 128)]
    private ?string $prenom = null;

    #[ORM\Column(length: 128)]
    private ?string $mail = null;

    #[ORM\ManyToOne(inversedBy: 'requetes')]
    private ?Groupes $groupe_principal = null;

    #[ORM\OneToOne(inversedBy: 'requetes', cascade: ['persist', 'remove'])]
    private ?Localisations $localisation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'referentDe')]
    private ?Employe $referent = null;

    #[ORM\ManyToOne(inversedBy: 'requetes')]
    private ?EtatsRequetes $etat_requete = null;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getGroupePrincipal(): ?Groupes
    {
        return $this->groupe_principal;
    }

    public function setGroupePrincipal(?Groupes $groupe_principal): static
    {
        $this->groupe_principal = $groupe_principal;

        return $this;
    }

    public function getLocalisation(): ?Localisations
    {
        return $this->localisation;
    }

    public function setLocalisation(?Localisations $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getReferent(): ?Employe
    {
        return $this->referent;
    }

    public function setReferent(?Employe $referent): static
    {
        $this->referent = $referent;

        return $this;
    }

    public function getEtatRequete(): ?EtatsRequetes
    {
        return $this->etat_requete;
    }

    public function setEtatRequete(?EtatsRequetes $etat_requete): static
    {
        $this->etat_requete = $etat_requete;

        return $this;
    }
}
