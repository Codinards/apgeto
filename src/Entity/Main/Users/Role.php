<?php

namespace App\Entity\Main\Users;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\Main\Users\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Illuminate\Support\Collection as SupportCollection;
use Serializable;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @UniqueEntity("name")
 * @UniqueEntity("title")
 * @ORM\Table("role")
 * 
 */
class Role implements Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Regex(
     * pattern="/^ROLE_([A-Z_]+)$/"
     * )
     * @Assert\NotBlank()
     * 
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * 
     */
    private string $title;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="role")
     */
    private Collection $users;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     * 
     */
    private bool $isDeletable = true;

    /**
     * @ORM\ManyToMany(targetEntity=UserAction::class, inversedBy="roles")
     */
    private Collection $userActions;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->autorisations = new ArrayCollection();
        $this->userActions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }

    public function getIsDeletable(): ?bool
    {
        return $this->isDeletable;
    }

    public function setIsDeletable(bool $isDeletable): self
    {
        $this->isDeletable = $isDeletable;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return Collection|UserAction[]
     */
    public function getUserActions(): Collection
    {
        return $this->userActions;
    }

    public function getSortedActions(): SupportCollection
    {
        return $this->collection($this->getUserActions()->toArray())
            ->sortBy(fn ($item) => $item->getName());
    }

    public function addUserAction(UserAction $userAction): self
    {
        if (!$this->userActions->contains($userAction)) {
            $this->userActions[] = $userAction;
            $userAction->addRole($this);
        }

        return $this;
    }

    public function removeUserAction(UserAction $userAction): self
    {
        if ($this->userActions->removeElement($userAction)) {
            $userAction->removeRole($this);
        }

        return $this;
    }

    public function hasUserAction(UserAction $userAction): bool
    {
        return $this->userActions->contains($userAction);
    }

    public function collection(array $data = []): SupportCollection
    {
        return new SupportCollection($data);
    }


    public function serialize()
    {
        return serialize(
            [
                'id' => $this->id,
                'name' => $this->name,
                'title' => $this->title
            ]
        );
    }

    public function unserialize($data)
    {
        $values = unserialize($data);
        $this->id = $values['id'];
        $this->name = $values['name'];
        $this->title = $values['title'];
    }

    public function hasRole(UserAction $action): bool
    {
        return $this->userActions->contains($action);
    }
}
