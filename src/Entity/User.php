<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="reporter")
     */
    private $reportedTasks;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="assignee")
     */
    private $assignedTasks;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="user")
     */
    private $taskCategories;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="supervisees")
     */
    private $manager;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="manager")
     */
    private $supervisees;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Task[]
     */
    public function getReportedTasks(): Collection
    {
        return $this->reportedTasks;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @return Collection|Category[]
     */
    public function getTaskCategories(): Collection
    {
        return $this->taskCategories;
    }

    public function addCategory(Category $taskCategory): self
    {
        if (!$this->taskCategories->contains($taskCategory)) {
            $this->taskCategories[] = $taskCategory;
            $taskCategory->setUser($this);
        }

        return $this;
    }

    public function removeCategory(Category $taskCategory): self
    {
        if ($this->taskCategories->removeElement($taskCategory)) {
            // set the owning side to null (unless already changed)
            if ($taskCategory->getUser() === $this) {
                $taskCategory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSupervisees(): Collection
    {
        return $this->supervisees;
    }

    public function getManager(): ?self
    {
        return $this->manager;
    }

    public function setManager(?self $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function addSupervisee(self $supervisee): self
    {
        if (!$this->supervisees->contains($supervisee)) {
            $this->supervisees[] = $supervisee;
            $supervisee->setManager($this);
        }

        return $this;
    }

    public function removeSupervisee(self $supervisee): self
    {
        if ($this->supervisees->removeElement($supervisee)) {
            // set the owning side to null (unless already changed)
            if ($supervisee->getManager() === $this) {
                $supervisee->setManager(null);
            }
        }

        return $this;
    }

    public function getManagedUsers(): array {
        $users = $this->getSupervisees()->toArray();
        array_unshift($users, $this);
        return $users;
    }
}
