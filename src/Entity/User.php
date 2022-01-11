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
     * @ORM\OneToMany(targetEntity=GanttTask::class, mappedBy="user")
     */
    private $ganttTasks;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="supervisees")
     */
    private $manager;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="manager")
     */
    private $supervisees;

    /**
     * @ORM\OneToMany(targetEntity=Audit::class, mappedBy="user")
     */
    private $audits;

    public function __construct()
    {
        $this->reportedTasks = new ArrayCollection();
        $this->assignedTasks = new ArrayCollection();
        $this->taskCategories = new ArrayCollection();
        $this->ganttTasks = new ArrayCollection();
        $this->supervisees = new ArrayCollection();
        $this->audits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function addReportedTask(Task $reportedTask): self
    {
        if (!$this->reportedTasks->contains($reportedTask)) {
            $this->reportedTasks[] = $reportedTask;
            $reportedTask->setReporter($this);
        }

        return $this;
    }

    public function removeReportedTask(Task $reportedTask): self
    {
        if ($this->reportedTasks->removeElement($reportedTask)) {
            // set the owning side to null (unless already changed)
            if ($reportedTask->getReporter() === $this) {
                $reportedTask->setReporter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getAssignedTasks(): Collection
    {
        return $this->assignedTasks;
    }

    public function addAssignedTask(Task $assignedTask): self
    {
        if (!$this->assignedTasks->contains($assignedTask)) {
            $this->assignedTasks[] = $assignedTask;
            $assignedTask->setAssignee($this);
        }

        return $this;
    }

    public function removeAssignedTask(Task $assignedTask): self
    {
        if ($this->assignedTasks->removeElement($assignedTask)) {
            // set the owning side to null (unless already changed)
            if ($assignedTask->getAssignee() === $this) {
                $assignedTask->setAssignee(null);
            }
        }

        return $this;
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
     * @return Collection|GanttTask[]
     */
    public function getGanttTasks(): Collection
    {
        return $this->ganttTasks;
    }

    public function addGanttTask(GanttTask $ganttTask): self
    {
        if (!$this->ganttTasks->contains($ganttTask)) {
            $this->ganttTasks[] = $ganttTask;
            $ganttTask->setUser($this);
        }

        return $this;
    }

    public function removeGanttTask(GanttTask $ganttTask): self
    {
        if ($this->ganttTasks->removeElement($ganttTask)) {
            // set the owning side to null (unless already changed)
            if ($ganttTask->getUser() === $this) {
                $ganttTask->setUser(null);
            }
        }

        return $this;
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

    /**
     * @return Collection|self[]
     */
    public function getSupervisees(): Collection
    {
        return $this->supervisees;
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

    /**
     * @return Collection|Audit[]
     */
    public function getAudits(): Collection
    {
        return $this->audits;
    }

    public function addAudit(Audit $audit): self
    {
        if (!$this->audits->contains($audit)) {
            $this->audits[] = $audit;
            $audit->setUser($this);
        }

        return $this;
    }

    public function removeAudit(Audit $audit): self
    {
        if ($this->audits->removeElement($audit)) {
            // set the owning side to null (unless already changed)
            if ($audit->getUser() === $this) {
                $audit->setUser(null);
            }
        }

        return $this;
    }
}
