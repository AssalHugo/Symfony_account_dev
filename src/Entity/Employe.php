<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: EmployeRepository::class)]
#[Vich\Uploadable]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $nom = null;

    #[ORM\Column(length: 64)]
    private ?string $prenom = null;

    #[Vich\UploadableField(mapping: 'employe_images', fileNameProperty: 'photo')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $syncReseda = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $page_pro = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $idhal = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $orcid = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $mail_secondaire = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephone_secondaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee_naissance = null;

    #[ORM\OneToMany(targetEntity: Contrats::class, mappedBy: 'employe')]
    private Collection $contrats;

    #[ORM\OneToOne(mappedBy: 'employe', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Localisations::class, inversedBy: 'employes', cascade: ['persist'])]
    private Collection $localisation;

    #[ORM\OneToMany(targetEntity: Telephones::class, mappedBy: 'employe')]
    private Collection $telephones;

    #[ORM\OneToMany(targetEntity: Groupes::class, mappedBy: 'responsable')]
    private Collection $responsable_de;

    #[ORM\ManyToMany(targetEntity: Groupes::class, mappedBy: 'adjoints')]
    private Collection $adjoint_de;

    #[ORM\ManyToMany(targetEntity: Groupes::class, inversedBy: 'employe_grp_secondaires', cascade: ['persist', 'remove'])]
    private Collection $groupes_secondaires;

    #[ORM\ManyToOne(inversedBy: 'employes_grp_principaux')]
    private ?Groupes $groupe_principal = null;

    #[ORM\OneToOne(mappedBy: 'responsable', cascade: ['persist', 'remove'])]
    private ?Departement $responsable_departement = null;

    #[ORM\OneToMany(targetEntity: Requetes::class, mappedBy: 'referent')]
    private Collection $referentDe;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'referent_de_
    employe')]
    private ?self $referent = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'referent')]
    private Collection $referent_de_employe;

    #[ORM\Column]
    private ?bool $redirection_mail = null;


    public function __construct()
    {
        $this->contrats = new ArrayCollection();
        $this->localisation = new ArrayCollection();
        $this->telephones = new ArrayCollection();
        $this->adjoint_de = new ArrayCollection();
        $this->groupes_secondaires = new ArrayCollection();
        $this->referentDe = new ArrayCollection();
        $this->responsable_de = new ArrayCollection();
        $this->referent_de_employe = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function isSyncReseda(): ?bool
    {
        return $this->syncReseda;
    }

    public function setSyncReseda(bool $syncReseda): static
    {
        $this->syncReseda = $syncReseda;

        return $this;
    }

    public function getPagePro(): ?string
    {
        return $this->page_pro;
    }

    public function setPagePro(?string $page_pro): static
    {
        $this->page_pro = $page_pro;

        return $this;
    }

    public function getIdhal(): ?string
    {
        return $this->idhal;
    }

    public function setIdhal(?string $idhal): static
    {
        $this->idhal = $idhal;

        return $this;
    }

    public function getOrcid(): ?string
    {
        return $this->orcid;
    }

    public function setOrcid(?string $orcid): static
    {
        $this->orcid = $orcid;

        return $this;
    }

    public function getMailSecondaire(): ?string
    {
        return $this->mail_secondaire;
    }

    public function setMailSecondaire(?string $mail_secondaire): static
    {
        $this->mail_secondaire = $mail_secondaire;

        return $this;
    }

    public function getTelephoneSecondaire(): ?string
    {
        return $this->telephone_secondaire;
    }

    public function setTelephoneSecondaire(?string $telephone_secondaire): static
    {
        $this->telephone_secondaire = $telephone_secondaire;

        return $this;
    }

    public function getAnneeNaissance(): ?int
    {
        return $this->annee_naissance;
    }

    public function setAnneeNaissance(?int $annee_naissance): static
    {
        $this->annee_naissance = $annee_naissance;

        return $this;
    }

    public function getSyncReseda(): ?string
    {
        return $this->syncReseda;
    }


    /**
     * @return Collection<int, Contrats>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrats $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setEmploye($this);
        }

        return $this;
    }

    public function removeContrat(Contrats $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getEmploye() === $this) {
                $contrat->setEmploye(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Localisations>
     */
    public function getLocalisation(): Collection
    {
        return $this->localisation;
    }

    public function addLocalisation(Localisations $localisation): static
    {
        if (!$this->localisation->contains($localisation)) {
            $this->localisation->add($localisation);
        }

        return $this;
    }

    public function removeLocalisation(Localisations $localisation): static
    {
        $this->localisation->removeElement($localisation);

        return $this;
    }

    /**
     * @return Collection<int, Telephones>
     */
    public function getTelephones(): Collection
    {
        return $this->telephones;
    }

    public function addTelephone(Telephones $telephone): static
    {
        if (!$this->telephones->contains($telephone)) {
            $this->telephones->add($telephone);
            $telephone->setEmploye($this);
        }

        return $this;
    }

    public function removeTelephone(Telephones $telephone): static
    {
        if ($this->telephones->removeElement($telephone)) {
            // set the owning side to null (unless already changed)
            if ($telephone->getEmploye() === $this) {
                $telephone->setEmploye(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupes>
     */
    public function getAdjointDe(): Collection
    {
        return $this->adjoint_de;
    }

    public function addAdjointDe(Groupes $adjointDe): static
    {
        if (!$this->adjoint_de->contains($adjointDe)) {
            $this->adjoint_de->add($adjointDe);
            $adjointDe->addAdjoint($this);
        }

        return $this;
    }

    public function removeAdjointDe(Groupes $adjointDe): static
    {
        if ($this->adjoint_de->removeElement($adjointDe)) {
            $adjointDe->removeAdjoint($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupes>
     */
    public function getGroupesSecondaires(): Collection
    {
        return $this->groupes_secondaires;
    }

    public function addGroupesSecondaire(Groupes $groupesSecondaire): static
    {
        if (!$this->groupes_secondaires->contains($groupesSecondaire)) {
            $this->groupes_secondaires->add($groupesSecondaire);
        }

        return $this;
    }

    public function removeGroupesSecondaire(Groupes $groupesSecondaire): static
    {
        $this->groupes_secondaires->removeElement($groupesSecondaire);

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

    public function getResponsableDepartement(): ?Departement
    {
        return $this->responsable_departement;
    }

    public function setResponsableDepartement(?Departement $responsable_departement): static
    {
        // unset the owning side of the relation if necessary
        if ($responsable_departement === null && $this->responsable_departement !== null) {
            $this->responsable_departement->setResponsable(null);
        }

        // set the owning side of the relation if necessary
        if ($responsable_departement !== null && $responsable_departement->getResponsable() !== $this) {
            $responsable_departement->setResponsable($this);
        }

        $this->responsable_departement = $responsable_departement;

        return $this;
    }

    /**
     * @return Collection<int, Requetes>
     */
    public function getReferentDe(): Collection
    {
        return $this->referentDe;
    }

    public function addReferentDe(Requetes $referentDe): static
    {
        if (!$this->referentDe->contains($referentDe)) {
            $this->referentDe->add($referentDe);
            $referentDe->setReferent($this);
        }

        return $this;
    }

    public function removeReferentDe(Requetes $referentDe): static
    {
        if ($this->referentDe->removeElement($referentDe)) {
            // set the owning side to null (unless already changed)
            if ($referentDe->getReferent() === $this) {
                $referentDe->setReferent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupes>
     */
    public function getResponsableDe(): Collection
    {
        return $this->responsable_de;
    }

    public function addResponsableDe(Groupes $responsableDe): static
    {
        if (!$this->responsable_de->contains($responsableDe)) {
            $this->responsable_de->add($responsableDe);
            $responsableDe->setResponsable($this);
        }

        return $this;
    }

    public function removeResponsableDe(Groupes $responsableDe): static
    {
        if ($this->responsable_de->removeElement($responsableDe)) {
            // set the owning side to null (unless already changed)
            if ($responsableDe->getResponsable() === $this) {
                $responsableDe->setResponsable(null);
            }
        }

        return $this;
    }

    public function getReferent(): ?self
    {
        return $this->referent;
    }

    public function setReferent(?self $referent): static
    {
        $this->referent = $referent;

        $referent->addReferentDeEmploye($this);

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getReferentDeEmploye(): Collection
    {
        return $this->referent_de_employe;
    }

    public function addReferentDeEmploye(Employe $referentDeEmploye): static
    {
        if (!$this->referent_de_employe->contains($referentDeEmploye)) {
            $this->referent_de_employe->add($referentDeEmploye);
            $referentDeEmploye->setReferent($this);
        }

        return $this;
    }

    public function removeReferentDeEmploye(Employe $referentDeEmploye): static
    {
        if ($this->referent_de_employe->removeElement($referentDeEmploye)) {
            // set the owning side to null (unless already changed)
            if ($referentDeEmploye->getReferent() === $this) {
                $referentDeEmploye->setReferent(null);
            }
        }

        return $this;
    }

    public function isRedirectionMail(): ?bool
    {
        return $this->redirection_mail;
    }

    public function setRedirectionMail(bool $redirection_mail): static
    {
        $this->redirection_mail = $redirection_mail;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}
