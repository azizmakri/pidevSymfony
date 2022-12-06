<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity
 */
class Publication
{
    /**
     * @var int
     *
     * @ORM\Column(name="idpub", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idpub;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datepub", type="date", nullable=true)
     */
    private $datepub;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbrecommentaire", type="integer", nullable=true)
     */
    private $nbrecommentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="topic", type="string", length=255, nullable=false)
     */
    private $topic;

    /**
     * @var int
     *
     * @ORM\Column(name="iduser", type="integer", nullable=false)
     */
    private $iduser;

    /**
     * @var float|null
     *
     * @ORM\Column(name="rating", type="float", precision=10, scale=0, nullable=true)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="string", length=255, nullable=false)
     */
    private $texte;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jaime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jaimepas;

    public function getIdpub(): ?int
    {
        return $this->idpub;
    }

    public function getDatepub(): ?\DateTimeInterface
    {
        return $this->datepub;
    }

    public function setDatepub(?\DateTimeInterface $datepub): self
    {
        $this->datepub = $datepub;

        return $this;
    }

    public function getNbrecommentaire(): ?int
    {
        return $this->nbrecommentaire;
    }

    public function setNbrecommentaire(?int $nbrecommentaire): self
    {
        $this->nbrecommentaire = $nbrecommentaire;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getJaime(): ?int
    {
        return $this->jaime;
    }

    public function setJaime(?int $jaime): self
    {
        $this->jaime = $jaime;

        return $this;
    }

    public function getJaimepas(): ?int
    {
        return $this->jaimepas;
    }

    public function setJaimepas(?int $jaimepas): self
    {
        $this->jaimepas = $jaimepas;

        return $this;
    }


}
