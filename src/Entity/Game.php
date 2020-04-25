<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="game")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="game")
     */
    private $rounds;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbWords;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="game")
     */
    private $points;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFinished;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\History", mappedBy="game")
     */
    private $histories;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->rounds = new ArrayCollection();
        $this->points = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

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

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setGame($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getGame() === $this) {
                $team->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setGame($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->contains($round)) {
            $this->rounds->removeElement($round);
            // set the owning side to null (unless already changed)
            if ($round->getGame() === $this) {
                $round->setGame(null);
            }
        }

        return $this;
    }

    public function getNbWords(): ?int
    {
        return $this->nbWords;
    }

    public function setNbWords(int $nbWords): self
    {
        $this->nbWords = $nbWords;

        return $this;
    }

    /**
     * @return Collection|Point[]
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): self
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
            $point->setGame($this);
        }

        return $this;
    }

    public function removePoint(Point $point): self
    {
        if ($this->points->contains($point)) {
            $this->points->removeElement($point);
            // set the owning side to null (unless already changed)
            if ($point->getGame() === $this) {
                $point->setGame(null);
            }
        }

        return $this;
    }

    public function getIsFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): self
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setGame($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
            // set the owning side to null (unless already changed)
            if ($history->getGame() === $this) {
                $history->setGame(null);
            }
        }

        return $this;
    }
}
