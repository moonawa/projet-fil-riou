<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"show","listercomptes"})
     *   *@Groups({"comptes"})
     */
    private $id;

   

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"comptes"})

     */
    private $solde;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="comptes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comptes"})
     */
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=255)
    * @Groups({"listercomptes"})
     *@Groups({"comptes"})
     */
    private $noCompte;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="compte")
     *   *@Groups({"comptes"})
     */
    private $depots;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getNoCompte(): ?string
    {
        return $this->noCompte;
    }

    public function setNoCompte(string $noCompte): self
    {
        $this->noCompte = $noCompte;

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
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }

    
}
