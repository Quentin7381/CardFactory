<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $css_class = null;

    #[ORM\Column]
    private ?int $size_x = null;

    #[ORM\Column]
    private ?int $size_y = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $background_image = null;

    /**
     * @var Collection<int, Card>
     */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'template', orphanRemoval: true)]
    private Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCssClass(): ?string
    {
        return $this->css_class;
    }

    public function setCssClass(string $css_class): static
    {
        $this->css_class = $css_class;

        return $this;
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

    public function getBacgroundImage(): ?string
    {
        return $this->background_image;
    }

    public function setBacgroundImage(?string $background_image): static
    {
        $this->background_image = $background_image;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setTemplate($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getTemplate() === $this) {
                $card->setTemplate(null);
            }
        }

        return $this;
    }
}
