<?php

namespace App\Entity\Main\Operations;

use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Repository\Main\Operations\OperationRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OperationRepository::class)
 * @ORM\Table("operation")
 */
class Operation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $inflows = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $outflows = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminOperations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    public $hasContributor = false;

    private $memberName;

    /**
     * @var int|string
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private int|string $year;

    /**
     * @ORM\OneToOne(targetEntity=Fund::class, mappedBy="operation", cascade={"persist", "remove"})
     */
    private $fund;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        $this->funds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getInflows(): ?int
    {
        return $this->inflows;
    }

    public function setInflows(int $inflows): self
    {
        $this->inflows = $inflows;
        $this->balance += $inflows;
        $this->getType()->setInflow($inflows);

        return $this;
    }

    public function getOutflows(): ?int
    {
        return $this->outflows;
    }

    public function setOutflows(int $outflows): self
    {
        $this->outflows = $outflows;
        $this->balance -= $outflows;
        $this->getType()->setOutflow($outflows);

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getMemberName(): ?array
    {
        if (null !== $this->memberName) {
            return explode(' :___: ', $this->memberName);
        }
        return $this->memberName;
    }

    public function getObservation(): ?string
    {
        $observation = $this->observation;
        if (null !== $observation) {
            $observation = explode(' ---member: ', $observation);
            if ($user = ($observation[1] ?? null)) {
                $this->hasContributor = true;
                $this->memberName = $observation[1];
            }
            return trim($observation[0]);
        }
        return $observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        $this->setYear($createdAt->format('Y'));

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getYear(): null|int|string
    {
        return (int) $this->year;
    }

    public function setYear(null|int|string $year): self
    {
        $this->year = (int) $year;

        return $this;
    }

    public function getFund(): ?Fund
    {
        return $this->fund;
    }

    public function setFund(?Fund $fund): self
    {
        // unset the owning side of the relation if necessary
        if ($fund === null && $this->fund !== null) {
            $this->fund->setOperation(null);
        }

        // set the owning side of the relation if necessary
        if ($fund !== null && $fund->getOperation() !== $this) {
            $fund->setOperation($this);
        }

        $this->fund = $fund;

        return $this;
    }

    public function isInflow(): bool
    {
        return $this->inflows > 0;
    }
}
