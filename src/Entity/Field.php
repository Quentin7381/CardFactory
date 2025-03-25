<?php

namespace App\Entity;

use App\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $size_x = null;

    #[ORM\Column]
    private ?int $size_y = null;

    #[ORM\Column]
    private ?int $pos_x = null;

    #[ORM\Column]
    private ?int $pos_y = null;

    #[ORM\Column(length: 255)]
    private ?string $class = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSizeX(): ?int
    {
        return $this->size_x;
    }

    public function setSizeX(int $size_x): static
    {
        $this->size_x = $size_x;

        return $this;
    }

    public function getSizeY(): ?int
    {
        return $this->size_y;
    }

    public function setSizeY(int $size_y): static
    {
        $this->size_y = $size_y;

        return $this;
    }

    public function getPosX(): ?int
    {
        return $this->pos_x;
    }

    public function setPosX(int $pos_x): static
    {
        $this->pos_x = $pos_x;

        return $this;
    }

    public function getPosY(): ?int
    {
        return $this->pos_y;
    }

    public function setPosY(int $pos_y): static
    {
        $this->pos_y = $pos_y;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;

        return $this;
    }
}
