<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecepteurRepository")
 */
class Recepteur
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
    private $prenomR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $NCIR;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomR;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telR;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenomR(): ?string
    {
        return $this->prenomR;
    }

    public function setPrenomR(string $prenomR): self
    {
        $this->prenomR = $prenomR;

        return $this;
    }

    public function getNCIR(): ?string
    {
        return $this->NCIR;
    }

    public function setNCIR(?string $NCIR): self
    {
        $this->NCIR = $NCIR;

        return $this;
    }

    public function getNomR(): ?string
    {
        return $this->nomR;
    }

    public function setNomR(string $nomR): self
    {
        $this->nomR = $nomR;

        return $this;
    }

    public function getTelR(): ?string
    {
        return $this->telR;
    }

    public function setTelR(string $telR): self
    {
        $this->telR = $telR;

        return $this;
    }
}
