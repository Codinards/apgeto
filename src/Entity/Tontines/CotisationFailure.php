<?php

namespace App\Entity\Tontines;

use App\Repository\Tontines\CotisationFailureRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationFailureRepository::class)
 * @ORM\Table("cotisation_failure")
 */
class CotisationFailure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Unity::class, inversedBy="cotisationStatuses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=TontineurData::class, inversedBy="cotisationFailures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontiner;

    /**
     * @ORM\ManyToOne(targetEntity=CotisationDay::class, inversedBy="cotisationFailures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cotisationDay;

    /**
     * @ORM\ManyToOne(targetEntity=Tontine::class, inversedBy="cotisationFailures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontine;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnity(): ?Unity
    {
        return $this->unity;
    }

    public function setUnity(?Unity $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTontiner(): ?TontineurData
    {
        return $this->tontiner;
    }

    public function setTontiner(?TontineurData $tontiner): self
    {
        $this->tontiner = $tontiner;

        return $this;
    }

    public function getCotisationDay(): ?CotisationDay
    {
        return $this->cotisationDay;
    }

    public function setCotisationDay(?CotisationDay $cotisationDay): self
    {
        $this->cotisationDay = $cotisationDay;

        return $this;
    }

    public function getTontine(): ?Tontine
    {
        return $this->tontine;
    }

    public function setTontine(?Tontine $tontine): self
    {
        $this->tontine = $tontine;

        return $this;
    }
}
