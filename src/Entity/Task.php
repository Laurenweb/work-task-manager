<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reportedTasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reporter;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assignedTasks")
     */
    private $assignee;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $expectedDuration;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $actualDuration;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="childrenTasks")
     */
    private $parentTask;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="parentTask")
     */
    private $childrenTasks;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="float")
     */
    private $wantedDuration;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="tasks")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Audit::class, mappedBy="task", orphanRemoval=true)
     */
    private $audits;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=TimeDetail::class, mappedBy="task", orphanRemoval=true)
     */
    private $timeDetails;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dueAt;

    /**
     * @ORM\ManyToOne(targetEntity=GanttTask::class, inversedBy="tasks")
     */
    private $project;

    public function __construct()
    {
        $this->childrenTasks = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->audits = new ArrayCollection();
        $this->timeDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): self
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getExpectedDuration(): ?float
    {
        return $this->expectedDuration;
    }

    public function setExpectedDuration(float $expectedDuration): self
    {
        $this->expectedDuration = $expectedDuration;

        return $this;
    }

    public function getActualDuration(): ?float
    {
        return $this->actualDuration;
    }

    public function setActualDuration(?float $actualDuration): self
    {
        $this->actualDuration = $actualDuration;

        return $this;
    }

    public function getParentTask(): ?self
    {
        return $this->parentTask;
    }

    public function setParentTask(?self $parentTask): self
    {
        $this->parentTask = $parentTask;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildrenTasks(): Collection
    {
        return $this->childrenTasks;
    }

    public function addChildrenTask(self $childrenTask): self
    {
        if (!$this->childrenTasks->contains($childrenTask)) {
            $this->childrenTasks[] = $childrenTask;
            $childrenTask->setParentTask($this);
        }

        return $this;
    }

    public function removeChildrenTask(self $childrenTask): self
    {
        if ($this->childrenTasks->removeElement($childrenTask)) {
            // set the owning side to null (unless already changed)
            if ($childrenTask->getParentTask() === $this) {
                $childrenTask->setParentTask(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getWantedDuration(): ?float
    {
        return $this->wantedDuration;
    }

    public function setWantedDuration(float $wantedDuration): self
    {
        $this->wantedDuration = $wantedDuration;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addTask($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeTask($this);
        }

        return $this;
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
            $audit->setTask($this);
        }

        return $this;
    }

    public function removeAudit(Audit $audit): self
    {
        if ($this->audits->removeElement($audit)) {
            // set the owning side to null (unless already changed)
            if ($audit->getTask() === $this) {
                $audit->setTask(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|TimeDetail[]
     */
    public function getTimeDetails(): Collection
    {
        return $this->timeDetails;
    }

    public function addTimeDetail(TimeDetail $timeDetail): self
    {
        if (!$this->timeDetails->contains($timeDetail)) {
            $this->timeDetails[] = $timeDetail;
            $timeDetail->setTask($this);
        }

        return $this;
    }

    public function removeTimeDetail(TimeDetail $timeDetail): self
    {
        if ($this->timeDetails->removeElement($timeDetail)) {
            // set the owning side to null (unless already changed)
            if ($timeDetail->getTask() === $this) {
                $timeDetail->setTask(null);
            }
        }

        return $this;
    }

    public function getDueAt(): ?\DateTime
    {
        return $this->dueAt;
    }

    public function setDueAt(?\DateTime $dueAt): self
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    public function getProject(): ?GanttTask
    {
        return $this->project;
    }

    public function setProject(?GanttTask $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function isLate(): bool {
        return $this->dueAt->format('Y-m-d') < (new DateTime())->format('Y-m-d');
    }
}
