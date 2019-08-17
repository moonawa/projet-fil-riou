<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\EmeteurRepository")
 */
class Emeteur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $NCIE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telE;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomE(): ?string
    {
        return $this->nomE;
    }

    public function setNomE(string $nomE): self
    {
        $this->nomE = $nomE;

        return $this;
    }

    public function getPrenomE(): ?string
    {
        return $this->prenomE;
    }

    public function setPrenomE(string $prenomE): self
    {
        $this->prenomE = $prenomE;

        return $this;
    }

    public function getNCIE(): ?string
    {
        return $this->NCIE;
    }

    public function setNCIE(?string $NCIE): self
    {
        $this->NCIE = $NCIE;

        return $this;
    }

    public function getTelE(): ?string
    {
        return $this->telE;
    }

    public function setTelE(string $telE): self
    {
        $this->telE = $telE;

        return $this;
    }
}
