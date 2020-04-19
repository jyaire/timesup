<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Team implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="teams")
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="round1winner")
     */
    private $rounds1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="round2winner")
     */
    private $rounds2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="round3winner")
     */
    private $rounds3;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="creator")
     */
    private $creates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="team")
     */
    private $points;

    public function __construct()
    {
        $this->rounds1 = new ArrayCollection();
        $this->rounds2 = new ArrayCollection();
        $this->rounds3 = new ArrayCollection();
        $this->creates = new ArrayCollection();
        $this->points = new ArrayCollection();
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds1(): Collection
    {
        return $this->rounds1;
    }

    public function addRounds1(Round $rounds1): self
    {
        if (!$this->rounds1->contains($rounds1)) {
            $this->rounds1[] = $rounds1;
            $rounds1->setRound1winner($this);
        }

        return $this;
    }

    public function removeRounds1(Round $rounds1): self
    {
        if ($this->rounds1->contains($rounds1)) {
            $this->rounds1->removeElement($rounds1);
            // set the owning side to null (unless already changed)
            if ($rounds1->getRound1winner() === $this) {
                $rounds1->setRound1winner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds2(): Collection
    {
        return $this->rounds2;
    }

    public function addRounds2(Round $rounds2): self
    {
        if (!$this->rounds2->contains($rounds2)) {
            $this->rounds2[] = $rounds2;
            $rounds2->setRound2winner($this);
        }

        return $this;
    }

    public function removeRounds2(Round $rounds2): self
    {
        if ($this->rounds2->contains($rounds2)) {
            $this->rounds2->removeElement($rounds2);
            // set the owning side to null (unless already changed)
            if ($rounds2->getRound2winner() === $this) {
                $rounds2->setRound2winner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds3(): Collection
    {
        return $this->rounds3;
    }

    public function addRounds3(Round $rounds3): self
    {
        if (!$this->rounds3->contains($rounds3)) {
            $this->rounds3[] = $rounds3;
            $rounds3->setRound3winner($this);
        }

        return $this;
    }

    public function removeRounds3(Round $rounds3): self
    {
        if ($this->rounds3->contains($rounds3)) {
            $this->rounds3->removeElement($rounds3);
            // set the owning side to null (unless already changed)
            if ($rounds3->getRound3winner() === $this) {
                $rounds3->setRound3winner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getCreates(): Collection
    {
        return $this->creates;
    }

    public function addCreate(Round $create): self
    {
        if (!$this->creates->contains($create)) {
            $this->creates[] = $create;
            $create->setCreator($this);
        }

        return $this;
    }

    public function removeCreate(Round $create): self
    {
        if ($this->creates->contains($create)) {
            $this->creates->removeElement($create);
            // set the owning side to null (unless already changed)
            if ($create->getCreator() === $this) {
                $create->setCreator(null);
            }
        }

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
            $point->setTeam($this);
        }

        return $this;
    }

    public function removePoint(Point $point): self
    {
        if ($this->points->contains($point)) {
            $this->points->removeElement($point);
            // set the owning side to null (unless already changed)
            if ($point->getTeam() === $this) {
                $point->setTeam(null);
            }
        }

        return $this;
    }
}
