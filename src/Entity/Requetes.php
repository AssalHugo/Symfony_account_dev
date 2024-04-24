<?php

namespace App\Entity;

use App\Repository\RequetesRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'requetes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contrats $contrat = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_requete = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_validation = null;

    #[ORM\ManyToOne(inversedBy: 'requetes')]
    private ?EtatSystemeRequete $etat_systeme_requete = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mdp_provisoire = null;

    #[ORM\ManyToOne(inversedBy: 'requetes')]
    private ?User $user_cree = null;


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

    public function getContrat(): ?Contrats
    {
        return $this->contrat;
    }

    public function setContrat(?Contrats $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateRequete(): ?\DateTimeInterface
    {
        return $this->date_requete;
    }

    public function setDateRequete(\DateTimeInterface $date_requete): static
    {
        $this->date_requete = $date_requete;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->date_validation;
    }

    public function setDateValidation(?\DateTimeInterface $date_validation): static
    {
        $this->date_validation = $date_validation;

        return $this;
    }

    public function getEtatSystemeRequete(): ?EtatSystemeRequete
    {
        return $this->etat_systeme_requete;
    }

    public function setEtatSystemeRequete(?EtatSystemeRequete $etat_systeme_requete): static
    {
        $this->etat_systeme_requete = $etat_systeme_requete;

        return $this;
    }

    public function getMdpProvisoire(): ?string
    {
        return $this->mdp_provisoire;
    }

    public function setMdpProvisoire(?string $mdp_provisoire): static
    {
        $this->mdp_provisoire = $mdp_provisoire;

        return $this;
    }

    public function getUserCree(): ?User
    {
        return $this->user_cree;
    }

    public function setUserCree(?User $user_cree): static
    {
        $this->user_cree = $user_cree;

        return $this;
    }
}
