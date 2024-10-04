<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Exercice>
     */
    #[ORM\ManyToMany(targetEntity: Exercice::class)]
    private Collection $Exercices;

    #[ORM\Column]
    private ?int $objective = null;

    /**
     * @var Collection<int, Tracking>
     */
    #[ORM\OneToMany(targetEntity: Tracking::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $Trackings;

    public function __construct()
    {
        $this->Exercices = new ArrayCollection();
        $this->Trackings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Exercice>
     */
    public function getExercices(): Collection
    {
        return $this->Exercices;
    }

    public function addExercices(Exercice $exercy): static
    {
        if (!$this->Exercices->contains($exercy)) {
            $this->Exercices->add($exercy);
        }

        return $this;
    }

    public function removeExercices(Exercice $exercy): static
    {
        $this->Exercices->removeElement($exercy);

        return $this;
    }

    public function getObjective(): ?int
    {
        return $this->objective;
    }

    public function setObjective(int $objective): static
    {
        $this->objective = $objective;

        return $this;
    }

    /**
     * @return Collection<int, Tracking>
     */
    public function getTrackings(): Collection
    {
        return $this->Trackings;
    }

    public function addTracking(Tracking $tracking): static
    {
        if (!$this->Trackings->contains($tracking)) {
            $this->Trackings->add($tracking);
            $tracking->setUser($this);
        }

        return $this;
    }

    public function removeTracking(Tracking $tracking): static
    {
        if ($this->Trackings->removeElement($tracking)) {
            // set the owning side to null (unless already changed)
            if ($tracking->getUser() === $this) {
                $tracking->setUser(null);
            }
        }

        return $this;
    }
}
