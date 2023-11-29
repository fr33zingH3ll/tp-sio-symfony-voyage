<?php

namespace App\Entity;

use App\Repository\EnvironmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvironmentRepository::class)]
class Environment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToOne(mappedBy: 'environment')]
    private ?Travel $travel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTravel(): ?Travel
    {
        return $this->travel;
    }

    public function setTravel(?Travel $travel): static
    {
        // unset the owning side of the relation if necessary
        if ($travel === null && $this->travel !== null) {
            $this->travel->setEnvironment(null);
        }

        // set the owning side of the relation if necessary
        if ($travel !== null && $travel->getEnvironment() !== $this) {
            $travel->setEnvironment($this);
        }

        $this->travel = $travel;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }
}
