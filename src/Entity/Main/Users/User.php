<?php

namespace App\Entity\Main\Users;

use App\Entity\Assistances\Contributor;
use App\Entity\Interests\UserInterest;
use App\Entity\Main\Funds\DebtAvalist;
use App\Entity\Main\Funds\DebtRenewal;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Entity\Tontines\Unity;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\Fund;
use App\Entity\Tontines\Tontine;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Main\Funds\Account;
use App\Entity\Tontines\Tontineur;
use Doctrine\Common\Collections\Collection;
use App\Repository\Main\Users\UserRepository;
use App\Entity\EntityTraits\GlobalMethodsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use DateTime;
use Njeaner\ImageUpload\Annotations\Uploadable;
use Njeaner\ImageUpload\Annotations\UploadableField;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("pseudo")
 * @UniqueEntity("telephone")
 * @ORM\Table("user")
 * @Uploadable()
 */
class User implements UserInterface, \Serializable
{
    use GlobalMethodsTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true, unique=true)
     * 
     */
    private $pseudo;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     * @Constraints\Length(min=8)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Constraints\NotBlank()
     * @Constraints\Length(min=3)
     * 
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $telephone;

    /**
     * @UploadableField(filename="image", path="/uploads/users")
     *
     * @var null|string|File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminUsers")
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="admin")
     */
    private $adminUsers;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $updatedAt;


    /**
     * @ORM\Column(type="boolean", options={"default":0})
     * 
     */
    private $locked = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     */
    private $lockedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="parrainedUsers")
     */
    private $parrain;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="parrain")
     */
    private $parrainedUsers;

    /**
     * @ORM\OneToOne(targetEntity=Account::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity=Account::class, mappedBy="admin")
     */
    private $adminAccounts;

    /**
     * @ORM\OneToMany(targetEntity=Fund::class, mappedBy="user", orphanRemoval=true)
     */
    private $funds;

    /**
     * @ORM\OneToMany(targetEntity=Fund::class, mappedBy="admin")
     */
    private $adminFunds;

    /**
     * @ORM\OneToMany(targetEntity=Debt::class, mappedBy="user", orphanRemoval=true)
     */
    private $debts;

    /**
     * @ORM\ManyToMany(targetEntity=Debt::class, mappedBy="avalistes")
     */
    private $debtsAvalistes;

    /**
     * @ORM\OneToMany(targetEntity=Debt::class, mappedBy="admin")
     */
    private $adminDebts;

    /**
     * @ORM\OneToMany(targetEntity=Type::class, mappedBy="admin")
     */
    private $adminOperationTypes;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="admin")
     */
    private $adminOperations;

    /**
     * @ORM\OneToMany(targetEntity=\App\Entity\Assistances\Assistance::class, mappedBy="user")
     */
    private $assistances;

    /**
     * @ORM\OneToMany(targetEntity=\App\Entity\Assistances\Assistance::class, mappedBy="admin")
     */
    private $adminAssistances;

    /**
     * @ORM\OneToMany(targetEntity=Contributor::class, mappedBy="user")
     */
    private $contributions;

    /**
     * @ORM\OneToMany(targetEntity=Tontine::class, mappedBy="admin")
     */
    private $adminTontines;

    /**
     * @ORM\OneToOne(targetEntity=Tontineur::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $tontineur;

    /**
     * @ORM\OneToMany(targetEntity=Tontineur::class, mappedBy="admin")
     */
    private $adminTontineurs;

    /**
     * @ORM\OneToMany(targetEntity=Unity::class, mappedBy="admin")
     */
    private $adminUnities;

    /**
     * @ORM\OneToMany(targetEntity=UserInterest::class, mappedBy="user")
     */
    private $userInterests;

    /**
     * @ORM\OneToMany(targetEntity=DebtRenewal::class, mappedBy="admin")
     */
    private $adminDebtRenewals;

    /**
     * @ORM\OneToMany(targetEntity=DebtAvalist::class, mappedBy="user")
     */
    private $avalisedDebts;



    public function __construct()
    {
        $this->adminUsers = new ArrayCollection();
        $this->parrainedUsers = new ArrayCollection();
        $this->adminTontines = new ArrayCollection();
        $this->tontineurs = new ArrayCollection();
        $this->adminTontineurs = new ArrayCollection();
        $this->adminUnities = new ArrayCollection();
        $this->adminAccounts = new ArrayCollection();
        $this->funds = new ArrayCollection();
        $this->adminFunds = new ArrayCollection();
        $this->debts = new ArrayCollection();
        $this->debtsAvalistes = new ArrayCollection();
        $this->adminDebts = new ArrayCollection();
        $this->adminOperationTypes = new ArrayCollection();
        $this->adminOperations = new ArrayCollection();
        $this->assistances = new ArrayCollection();
        $this->adminAssistances = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->userInterests = new ArrayCollection();
        $this->adminDebtRenewals = new ArrayCollection();
        $this->avalisedDebts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getName(): string
    {
        return (string) $this->username;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $role = $this->getRole();
        $roles = [$role ? $role->getName() : null];
        // guarantee every user at least has ROLE_USER

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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdmin(): ?self
    {
        return $this->admin;
    }

    public function setAdmin(?self $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAdminUsers(): Collection
    {
        return $this->adminUsers;
    }

    public function addAdminUser(self $adminUser): self
    {
        if (!$this->adminUsers->contains($adminUser)) {
            $this->adminUsers[] = $adminUser;
            $adminUser->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminUser(self $adminUser): self
    {
        if ($this->adminUsers->removeElement($adminUser)) {
            // set the owning side to null (unless already changed)
            if ($adminUser->getAdmin() === $this) {
                $adminUser->setAdmin(null);
            }
        }

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

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getLockedAt(): ?\DateTimeInterface
    {
        return $this->lockedAt;
    }

    public function setLockedAt(?\DateTimeInterface $lockedAt): self
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getParrain(): ?self
    {
        return $this->parrain;
    }

    public function setParrain(?self $parrain): self
    {
        $this->parrain = $parrain;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getParrainedUsers(): Collection
    {
        return $this->parrainedUsers;
    }

    public function addParrainedUser(self $parrainedUser): self
    {
        if (!$this->parrainedUsers->contains($parrainedUser)) {
            $this->parrainedUsers[] = $parrainedUser;
            $parrainedUser->setParrain($this);
        }

        return $this;
    }

    public function removeParrainedUser(self $parrainedUser): self
    {
        if ($this->parrainedUsers->removeElement($parrainedUser)) {
            // set the owning side to null (unless already changed)
            if ($parrainedUser->getParrain() === $this) {
                $parrainedUser->setParrain(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        // set the owning side of the relation if necessary
        if ($account->getUser() !== $this) {
            $account->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Account[]
     */
    public function getAdminAccounts(): Collection
    {
        return $this->adminAccounts;
    }

    public function addAdminAccount(Account $adminAccount): self
    {
        if (!$this->adminAccounts->contains($adminAccount)) {
            $this->adminAccounts[] = $adminAccount;
            $adminAccount->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminAccount(Account $adminAccount): self
    {
        if ($this->adminAccounts->removeElement($adminAccount)) {
            // set the owning side to null (unless already changed)
            if ($adminAccount->getAdmin() === $this) {
                $adminAccount->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fund[]
     */
    public function getFunds(): Collection
    {
        return $this->funds;
    }

    public function addFund(Fund $fund): self
    {
        if (!$this->funds->contains($fund)) {
            $this->funds[] = $fund;
            $fund->setUser($this);
        }

        return $this;
    }

    public function removeFund(Fund $fund): self
    {
        if ($this->funds->removeElement($fund)) {
            // set the owning side to null (unless already changed)
            if ($fund->getUser() === $this) {
                $fund->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fund[]
     */
    public function getAdminFunds(): Collection
    {
        return $this->adminFunds;
    }

    public function addAdminFund(Fund $adminFund): self
    {
        if (!$this->adminFunds->contains($adminFund)) {
            $this->adminFunds[] = $adminFund;
            $adminFund->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminFund(Fund $adminFund): self
    {
        if ($this->adminFunds->removeElement($adminFund)) {
            // set the owning side to null (unless already changed)
            if ($adminFund->getAdmin() === $this) {
                $adminFund->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Debt[]
     */
    public function getDebts(): Collection
    {
        return $this->debts;
    }

    public function addDebt(Debt $debt): self
    {
        if (!$this->debts->contains($debt)) {
            $this->debts[] = $debt;
            $debt->setUser($this);
        }

        return $this;
    }

    public function removeDebt(Debt $debt): self
    {
        if ($this->debts->removeElement($debt)) {
            // set the owning side to null (unless already changed)
            if ($debt->getUser() === $this) {
                $debt->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Debt[]
     */
    public function getDebtsAvalistes(): Collection
    {
        return $this->debtsAvalistes;
    }

    public function addDebtsAvaliste(Debt $debtsAvaliste): self
    {
        if (!$this->debtsAvalistes->contains($debtsAvaliste)) {
            $this->debtsAvalistes[] = $debtsAvaliste;
            $debtsAvaliste->addAvaliste($this);
        }

        return $this;
    }

    public function removeDebtsAvaliste(Debt $debtsAvaliste): self
    {
        if ($this->debtsAvalistes->removeElement($debtsAvaliste)) {
            $debtsAvaliste->removeAvaliste($this);
        }

        return $this;
    }

    /**
     * @return Collection|Debt[]
     */
    public function getAdminDebts(): Collection
    {
        return $this->adminDebts;
    }

    public function addAdminDebt(Debt $adminDebt): self
    {
        if (!$this->adminDebts->contains($adminDebt)) {
            $this->adminDebts[] = $adminDebt;
            $adminDebt->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminDebt(Debt $adminDebt): self
    {
        if ($this->adminDebts->removeElement($adminDebt)) {
            // set the owning side to null (unless already changed)
            if ($adminDebt->getAdmin() === $this) {
                $adminDebt->setAdmin(null);
            }
        }

        return $this;
    }

    public function canLoan(bool $throwException = true): bool
    {
        return $this->account->canLoan($throwException);
    }

    /**
     * Get the value of imageFile
     *
     * @return  null|File
     */
    public function getImageFile(): null|string|File
    {
        return $this->imageFile;
    }

    public function getImageFilename(): string
    {
        return $this->imageFile !== null ? $this->imageFile->getPathname() : '';
    }

    /**
     * Set the value of imageFile
     *
     * @param  null|File $imageFile
     *
     * @return  self
     */
    public function setImageFile(null|string|File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function resolveUpdatedAt(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * Get the value of image
     *
     * @return  string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @param  string  $image
     *
     * @return  self
     */
    public function setImage(?string $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Type[]
     */
    public function getAdminOperationTypes(): Collection
    {
        return $this->adminOperationTypes;
    }

    public function addAdminOperationType(Type $adminOperationType): self
    {
        if (!$this->adminOperationTypes->contains($adminOperationType)) {
            $this->adminOperationTypes[] = $adminOperationType;
            $adminOperationType->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminOperationType(Type $adminOperationType): self
    {
        if ($this->adminOperationTypes->removeElement($adminOperationType)) {
            // set the owning side to null (unless already changed)
            if ($adminOperationType->getAdmin() === $this) {
                $adminOperationType->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getAdminOperations(): Collection
    {
        return $this->adminOperations;
    }

    public function addAdminOperation(Operation $adminOperation): self
    {
        if (!$this->adminOperations->contains($adminOperation)) {
            $this->adminOperations[] = $adminOperation;
            $adminOperation->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminOperation(Operation $adminOperation): self
    {
        if ($this->adminOperations->removeElement($adminOperation)) {
            // set the owning side to null (unless already changed)
            if ($adminOperation->getAdmin() === $this) {
                $adminOperation->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|\App\Entity\Assistances\Assistance[]
     */
    public function getAssistances(): Collection
    {
        return $this->assistances;
    }

    public function addAssistance(\App\Entity\Assistances\Assistance $assistance): self
    {
        if (!$this->assistances->contains($assistance)) {
            $this->assistances[] = $assistance;
            $assistance->setUser($this);
        }

        return $this;
    }

    public function removeAssistance(\App\Entity\Assistances\Assistance $assistance): self
    {
        if ($this->assistances->removeElement($assistance)) {
            // set the owning side to null (unless already changed)
            if ($assistance->getUser() === $this) {
                $assistance->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|\App\Entity\Assistances\Assistance[]
     */
    public function getAdminAssistances(): Collection
    {
        return $this->adminAssistances;
    }

    public function addAdminAssistance(\App\Entity\Assistances\Assistance $adminAssistance): self
    {
        if (!$this->adminAssistances->contains($adminAssistance)) {
            $this->adminAssistances[] = $adminAssistance;
            $adminAssistance->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminAssistance(\App\Entity\Assistances\Assistance $adminAssistance): self
    {
        if ($this->adminAssistances->removeElement($adminAssistance)) {
            // set the owning side to null (unless already changed)
            if ($adminAssistance->getAdmin() === $this) {
                $adminAssistance->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contributor[]
     */
    public function getContributions(): Collection
    {
        return $this->contributions;
    }

    public function addContribution(Contributor $contribution): self
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setUser($this);
        }

        return $this;
    }

    public function removeContribution(Contributor $contribution): self
    {
        if ($this->contributions->removeElement($contribution)) {
            // set the owning side to null (unless already changed)
            if ($contribution->getUser() === $this) {
                $contribution->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tontine[]
     */
    public function getAdminTontines(): Collection
    {
        return $this->adminTontines;
    }

    public function addAdminTontine(Tontine $adminTontine): self
    {
        if (!$this->adminTontines->contains($adminTontine)) {
            $this->adminTontines[] = $adminTontine;
            $adminTontine->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminTontine(Tontine $adminTontine): self
    {
        if ($this->adminTontines->removeElement($adminTontine)) {
            // set the owning side to null (unless already changed)
            if ($adminTontine->getAdmin() === $this) {
                $adminTontine->setAdmin(null);
            }
        }

        return $this;
    }

    public function getTontineur(): ?Tontineur
    {
        return $this->tontineur;
    }

    public function setTontineur(Tontineur $tontineur): self
    {
        // set the owning side of the relation if necessary
        if ($tontineur->getUser() !== $this) {
            $tontineur->setUser($this);
        }

        $this->tontineur = $tontineur;

        return $this;
    }

    /**
     * @return Collection|Tontineur[]
     */
    public function getAdminTontineurs(): Collection
    {
        return $this->adminTontineurs;
    }

    public function addAdminTontineur(Tontineur $adminTontineur): self
    {
        if (!$this->adminTontineurs->contains($adminTontineur)) {
            $this->adminTontineurs[] = $adminTontineur;
            $adminTontineur->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminTontineur(Tontineur $adminTontineur): self
    {
        if ($this->adminTontineurs->removeElement($adminTontineur)) {
            // set the owning side to null (unless already changed)
            if ($adminTontineur->getAdmin() === $this) {
                $adminTontineur->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Unity[]
     */
    public function getAdminUnities(): Collection
    {
        return $this->adminUnities;
    }

    public function addAdminUnity(Unity $adminUnity): self
    {
        if (!$this->adminUnities->contains($adminUnity)) {
            $this->adminUnities[] = $adminUnity;
            $adminUnity->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminUnity(Unity $adminUnity): self
    {
        if ($this->adminUnities->removeElement($adminUnity)) {
            // set the owning side to null (unless already changed)
            if ($adminUnity->getAdmin() === $this) {
                $adminUnity->setAdmin(null);
            }
        }

        return $this;
    }

    public function hasRole(UserAction $action): bool
    {
        return $this->getRole()->hasRole($action);
    }

    public function hasAction(string $name): bool
    {
        $hasAction = false;
        foreach ($this->getRole()->getUserActions() as $action) {
            if ($action->getName() === $name) $hasAction = true;
        }
        return $hasAction;
    }

    /**
     * @return Collection|UserInterest[]
     */
    public function getUserInterests(): Collection
    {
        return $this->userInterests;
    }

    public function addUserInterest(UserInterest $userInterest): self
    {
        if (!$this->userInterests->contains($userInterest)) {
            $this->userInterests[] = $userInterest;
            $userInterest->setUser($this);
        }

        return $this;
    }

    public function removeUserInterest(UserInterest $userInterest): self
    {
        if ($this->userInterests->removeElement($userInterest)) {
            // set the owning side to null (unless already changed)
            if ($userInterest->getUser() === $this) {
                $userInterest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DebtRenewal[]
     */
    public function getAdminDebtRenewals(): Collection
    {
        return $this->adminDebtRenewals;
    }

    public function addAdminDebtRenewal(DebtRenewal $adminDebtRenewal): self
    {
        if (!$this->adminDebtRenewals->contains($adminDebtRenewal)) {
            $this->adminDebtRenewals[] = $adminDebtRenewal;
            $adminDebtRenewal->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminDebtRenewal(DebtRenewal $adminDebtRenewal): self
    {
        if ($this->adminDebtRenewals->removeElement($adminDebtRenewal)) {
            // set the owning side to null (unless already changed)
            if ($adminDebtRenewal->getAdmin() === $this) {
                $adminDebtRenewal->setAdmin(null);
            }
        }

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->pseudo,
            $this->password,
            $this->username,
            $this->address,
            $this->telephone,
            $this->image,
            $this->createdAt,
            $this->updatedAt,
            $this->locked,
            $this->lockedAt,
            $this->role,
            $this->parrain,
            $this->account,
            $this->tontineur
        ]);
    }

    public function unserialize(string $data)
    {
        list(
            $this->id,
            $this->pseudo,
            $this->password,
            $this->username,
            $this->address,
            $this->telephone,
            $this->image,
            $this->createdAt,
            $this->updatedAt,
            $this->locked,
            $this->lockedAt,
            $this->role,
            $this->parrain,
            $this->account,
            $this->tontineur
        ) = unserialize($data);
    }

    /**
     * @return Collection|DebtAvalist[]
     */
    public function getAvalisedDebts(): Collection
    {
        return $this->avalisedDebts;
    }

    public function addAvalisedDebt(DebtAvalist $avalisedDebt): self
    {
        if (!$this->avalisedDebts->contains($avalisedDebt)) {
            $this->avalisedDebts[] = $avalisedDebt;
            $avalisedDebt->setUser($this);
        }

        return $this;
    }

    public function removeAvalisedDebt(DebtAvalist $avalisedDebt): self
    {
        if ($this->avalisedDebts->removeElement($avalisedDebt)) {
            // set the owning side to null (unless already changed)
            if ($avalisedDebt->getUser() === $this) {
                $avalisedDebt->setUser(null);
            }
        }

        return $this;
    }
}
