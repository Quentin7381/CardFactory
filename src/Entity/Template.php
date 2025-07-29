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

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Card>
     */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'template', orphanRemoval: true)]
    private Collection $card;

    public function __construct()
    {
        $this->card = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCard(): Collection
    {
        return $this->card;
    }

    public function addCard(Card $card): static
    {
        if (!$this->card->contains($card)) {
            $this->card->add($card);
            $card->setTemplate($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->card->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getTemplate() === $this) {
                $card->setTemplate(null);
            }
        }

        return $this;
    }
}
