<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointRepository")
 */
class Point
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $point;

    /**
     * @ORM\Column(type="integer")
     */
    private $roundnb;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="points")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="points")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): self
    {
        $this->point = $point;

        return $this;
    }

    public function getRoundnb(): ?int
    {
        return $this->roundnb;
    }

    public function setRoundnb(int $roundnb): self
    {
        $this->roundnb = $roundnb;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }
}
