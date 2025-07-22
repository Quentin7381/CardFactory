<?php

namespace App\Entity;

use App\Repository\JWTRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JWTRepository::class)]
class JWT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $jti = null;

    #[ORM\ManyToOne(inversedBy: 'JWTs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $exp = null;

    #[ORM\Column(length: 900)]
    private ?string $fullstring = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJti(): ?string
    {
        return $this->jti;
    }

    public function setJti(string $jti): static
    {
        $this->jti = $jti;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function setExp(int $exp): static
    {
        $this->exp = $exp;

        return $this;
    }

    public function getFullstring(): ?string
    {
        return $this->fullstring;
    }

    public function setFullstring(string $fullstring): static
    {
        $this->fullstring = $fullstring;

        return $this;
    }
}
