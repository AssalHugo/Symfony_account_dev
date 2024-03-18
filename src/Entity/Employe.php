<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;


#[ORM\Entity(repositoryClass: EmployeRepository::class)]
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

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

    #[ORM\ManyToMany(targetEntity: Localisations::class, inversedBy: 'employes')]
    private Collection $localisation;

    #[ORM\OneToMany(targetEntity: Telephones::class, mappedBy: 'employe')]
    private Collection $telephones;

    #[ORM\OneToOne(mappedBy: 'responsable', cascade: ['persist', 'remove'])]
    private ?Groupes $reponsable_de = null;

    #[ORM\ManyToMany(targetEntity: Groupes::class, mappedBy: 'adjoints')]
    private Collection $adjoint_de;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Groupes $groupe_principal;

    #[ORM\ManyToMany(targetEntity: Groupes::class, inversedBy: 'employe_grp_secondaires')]
    private Collection $groupes_secondaires;


    public function __construct()
    {
        $this->contrats = new ArrayCollection();
        $this->localisation = new ArrayCollection();
        $this->telephones = new ArrayCollection();
        $this->adjoint_de = new ArrayCollection();
        $this->groupes_secondaires = new ArrayCollection();
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

    public function getReponsableDe(): ?Groupes
    {
        return $this->reponsable_de;
    }

    public function setReponsableDe(Groupes $reponsable_de): static
    {
        // set the owning side of the relation if necessary
        if ($reponsable_de->getResponsable() !== $this) {
            $reponsable_de->setResponsable($this);
        }

        $this->reponsable_de = $reponsable_de;

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

    public function getGroupePrincipal(): ?Groupes
    {
        return $this->groupe_principal;
    }

    public function setGroupePrincipal(Groupes $groupe_principal): static
    {
        $this->groupe_principal = $groupe_principal;

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
}
