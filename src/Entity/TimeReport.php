<?php

namespace App\Entity;

use App\Repository\TimeReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeReportRepository::class)
 */
class TimeReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $startingAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $endingAt;

    /**
     * @ORM\OneToMany(targetEntity=TimeDetail::class, mappedBy="timeReport", orphanRemoval=true)
     */
    private $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartingAt(): ?\DateTimeImmutable
    {
        return $this->startingAt;
    }

    public function setStartingAt(\DateTimeImmutable $startingAt): self
    {
        $this->startingAt = $startingAt;

        return $this;
    }

    public function getEndingAt(): ?\DateTimeImmutable
    {
        return $this->endingAt;
    }

    public function setEndingAt(\DateTimeImmutable $endingAt): self
    {
        $this->endingAt = $endingAt;

        return $this;
    }

    /**
     * @return Collection|TimeDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(TimeDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setTimeReport($this);
        }

        return $this;
    }

    public function removeDetail(TimeDetail $detail): self
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getTimeReport() === $this) {
                $detail->setTimeReport(null);
            }
        }

        return $this;
    }
}
