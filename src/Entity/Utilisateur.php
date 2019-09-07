<?php
namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert; //pour la validation des données
/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @Vich\Uploadable
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list", "show"})
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"list", "show"})
     */
    private $username;
    /**
     * @ORM\Column(type="json")
     * @Groups({"list", "show"})
     */
    private $roles = [];
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
    *@Assert\EqualTo(propertyPath="password",message="Les mots de passes ne correspondent pas !")
    */
    private $confirmPassword; //créé le getter et setter!

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list", "show"})
     */
    private $Entreprise;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "show"})
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, unique=false)
     * @Groups({"list", "show"})
     */
    private $Email;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $Telephone;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"list", "show"})
     */
    private $Nci;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="UserEmetteur")
     */
    private $envois;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="UserRecepteur")
     */
    private $retraits;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"list", "show"})
     */
    private $Status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profil", inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"list", "show"})
     */
    private $Profil;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="caissier")
     */
    private $depots;

    // ... other fields

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="imageName")
     * 
     * @var File
     */
    private $imageFile;
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;
    /*
     * @ORM\Column(type="datetime", nullable=true)

     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte")
     * 
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }  
    public function getConfirmPassword(): string
    {
        return (string) $this->confirmPassword;
    }
    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->Entreprise;
    }

    public function setEntreprise(?Entreprise $Entreprise): self
    {
        $this->Entreprise = $Entreprise;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->Telephone;
    }

    public function setTelephone(int $Telephone): self
    {
        $this->Telephone = $Telephone;

        return $this;
    }

    public function getNci(): ?string
    {
        return $this->Nci;
    }

    public function setNci(string $Nci): self
    {
        $this->Nci = $Nci;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getEnvois(): Collection
    {
        return $this->envois;
    }

    public function addEnvois(Transaction $envois): self
    {
        if (!$this->envois->contains($envois)) {
            $this->envois[] = $envois;
            $envois->setUserEmetteur($this);
        }

        return $this;
    }

    public function removeEnvois(Transaction $envois): self
    {
        if ($this->envois->contains($envois)) {
            $this->envois->removeElement($envois);
            // set the owning side to null (unless already changed)
            if ($envois->getUserEmetteur() === $this) {
                $envois->setUserEmetteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetraits(): Collection
    {
        return $this->retraits;
    }

    public function addRetrait(Transaction $retrait): self
    {
        if (!$this->retraits->contains($retrait)) {
            $this->retraits[] = $retrait;
            $retrait->setUserRecepteur($this);
        }

        return $this;
    }

    public function removeRetrait(Transaction $retrait): self
    {
        if ($this->retraits->contains($retrait)) {
            $this->retraits->removeElement($retrait);
            // set the owning side to null (unless already changed)
            if ($retrait->getUserRecepteur() === $this) {
                $retrait->setUserRecepteur(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->Profil;
    }

    public function setProfil(?Profil $Profil): self
    {
        $this->Profil = $Profil;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCaissier($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCaissier() === $this) {
                $depot->setCaissier(null);
            }
        }

        return $this;
    }
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param null |File $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($this->imageFile instanceof UploadFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
   
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    

    public function __construct()
    {
        $this->envois = new ArrayCollection();
        $this->retraits = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}
