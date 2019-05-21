<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
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
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="Site", orphanRemoval=true)
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="site", orphanRemoval=true)
     */
    private $Sortie;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->Sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setSite($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getSite() === $this) {
                $participant->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortie(): Collection
    {
        return $this->Sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->Sortie->contains($sortie)) {
            $this->Sortie[] = $sortie;
            $sortie->setSite($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->Sortie->contains($sortie)) {
            $this->Sortie->removeElement($sortie);
            // set the owning side to null (unless already changed)
            if ($sortie->getSite() === $this) {
                $sortie->setSite(null);
            }
        }

        return $this;
    }
}