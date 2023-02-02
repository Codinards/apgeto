<?php

namespace App\Entity\Assistances;

use App\Entity\Exceptions\EntityException;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Repository\Assistances\ContributorRepository;
use App\Tools\Entity\StaticEntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// user ManyToOne
/**
 * @ORM\Entity(repositoryClass=ContributorRepository::class)
 * @ORM\Table("contributor")
 */
class Contributor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount = 0;

    private $account;

    private $select;

    private $index;

    /**
     * @ORM\ManyToOne(targetEntity=Assistance::class, inversedBy="contributors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $assistance;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Fund::class, mappedBy="assistance", cascade={"persist", "remove"})
     */
    private $fund;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function mergeAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Get the value of select
     */
    public function getSelect(): bool
    {
        return $this->select;
    }

    /**
     * Set the value of select
     *
     * @return  self
     */
    public function setSelect(bool $select): self
    {
        $this->select = $select;

        return $this;
    }

    public function getAccount(): Account
    {
        if (is_null($this->account)) {
            $this->account = $this->getUser()->getAccount();
        }
        return $this->account;
    }

    // public function getFund(): ?int
    // {
    //     return $this->fund;
    // }

    // public function setFund(?int $fund): self
    // {
    //     $this->fund = $fund;

    //     return $this;
    // }

    /**
     * Get the value of index
     */
    public function getIndex(): ?int
    {
        return $this->index;
    }

    /**
     * Set the value of index
     *
     * @return  self
     */
    public function setIndex(?int $index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get the value of contributor
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * Set the value of contributor
     *
     * @return  self
     */
    public function setContributor($contributor)
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getAssistance(): ?Assistance
    {
        return $this->assistance;
    }

    public function setAssistance(?Assistance $assistance): self
    {
        $this->assistance = $assistance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString()
    {
        return $this->user->getUsername();
    }

    public function getFund(): ?Fund
    {
        return $this->fund;
    }

    public function setFund(?Fund $fund): self
    {
        // unset the owning side of the relation if necessary
        if ($fund === null && $this->fund !== null) {
            $this->fund->setAssistance(null);
        }

        // set the owning side of the relation if necessary
        if ($fund !== null && $fund->getAssistance() !== $this) {
            $fund->setAssistance($this);
        }

        $this->fund = $fund;

        return $this;
    }
}
