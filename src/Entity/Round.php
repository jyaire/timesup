<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoundRepository")
 */
class Round
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="rounds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="round2winner")
     */
    private $round1winner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="rounds2")
     */
    private $round2winner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="rounds3")
     */
    private $round3winner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="creates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $word;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRound1winner(): ?Team
    {
        return $this->round1winner;
    }

    public function setRound1winner(?Team $round1winner): self
    {
        $this->round1winner = $round1winner;

        return $this;
    }

    public function getRound2winner(): ?Team
    {
        return $this->round2winner;
    }

    public function setRound2winner(?Team $round2winner): self
    {
        $this->round2winner = $round2winner;

        return $this;
    }

    public function getRound3winner(): ?Team
    {
        return $this->round3winner;
    }

    public function setRound3winner(?Team $round3winner): self
    {
        $this->round3winner = $round3winner;

        return $this;
    }

    public function getCreator(): ?Team
    {
        return $this->creator;
    }

    public function setCreator(?Team $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }
}
