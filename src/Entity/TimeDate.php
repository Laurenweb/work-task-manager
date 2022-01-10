<?php

namespace App\Entity;

use App\Repository\TimeDateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeDateRepository::class)
 */
class TimeDate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity=TimeDetail::class, inversedBy="timeDates", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $timeDetail;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getTimeDetail(): ?TimeDetail
    {
        return $this->timeDetail;
    }

    public function setTimeDetail(?TimeDetail $timeDetail): self
    {
        $this->timeDetail = $timeDetail;

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
}
