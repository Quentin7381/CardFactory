<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;


    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $template = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $card_title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $card_subtitle = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $card_body = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $card_image = null;

    public function __construct()
    {
        $this->shared_with = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getCardTitle(): ?string
    {
        return $this->card_title;
    }

    public function setCardTitle(?string $card_title): static
    {
        $this->card_title = $card_title;

        return $this;
    }

    public function getCardSubtitle(): ?string
    {
        return $this->card_subtitle;
    }

    public function setCardSubtitle(?string $card_subtitle): static
    {
        $this->card_subtitle = $card_subtitle;

        return $this;
    }

    public function getCardBody(): ?string
    {
        return $this->card_body;
    }

    public function setCardBody(?string $card_body): static
    {
        $this->card_body = $card_body;

        return $this;
    }

    public function getCardImage(): ?string
    {
        return $this->card_image;
    }

    public function setCardImage(?string $card_image): static
    {
        $this->card_image = $card_image;

        return $this;
    }
}
