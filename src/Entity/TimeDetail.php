<?php

namespace App\Entity;

use App\Repository\TimeDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeDetailRepository::class)
 */
class TimeDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="timeDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity=TimeReport::class, inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $timeReport;

    /**
     * @ORM\OneToMany(targetEntity=TimeDate::class, mappedBy="timeDetail", cascade={"persist"}, orphanRemoval=true)
     */
    private $timeDates;

    public function __construct()
    {
        $this->timeDates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getTimeReport(): ?TimeReport
    {
        return $this->timeReport;
    }

    public function setTimeReport(?TimeReport $timeReport): self
    {
        $this->timeReport = $timeReport;

        return $this;
    }

    /**
     * @return Collection|TimeDate[]
     */
    public function getTimeDates(): Collection
    {
        return $this->timeDates;
    }

    public function addTimeDate(TimeDate $timeDate): self
    {
        if (!$this->timeDates->contains($timeDate)) {
            $this->timeDates[] = $timeDate;
            $timeDate->setTimeDetail($this);
        }

        return $this;
    }

    public function removeTimeDate(TimeDate $timeDate): self
    {
        if ($this->timeDates->removeElement($timeDate)) {
            // set the owning side to null (unless already changed)
            if ($timeDate->getTimeDetail() === $this) {
                $timeDate->setTimeDetail(null);
            }
        }

        return $this;
    }
}
