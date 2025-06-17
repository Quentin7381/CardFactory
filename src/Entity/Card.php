<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card implements \App\Entity\Interface\OrderableInterface
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

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'shared_cards')]
    #[ORM\JoinTable(name: 'card_shared_with')]
    private Collection $shared_with;

    public function __construct()
    {
        $this->shared_with = new ArrayCollection();
    }

    public function getSharedWith(): Collection
    {
        return $this->shared_with;
    }

    public function addSharedWith(User $user): static
    {
        if (!$this->shared_with->contains($user)) {
            $this->shared_with[] = $user;
            $user->getSharedCards()->add($this);
        }

        return $this;
    }

    public function removeSharedWith(User $user): static
    {
        if ($this->shared_with->removeElement($user)) {
            $user->getSharedCards()->removeElement($this);
        }

        return $this;
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

    // ----- CUSTOM METHODS -----

    public function getPrice(): ?int
    {
        $prices = [
            'default' => 500,
        ];

        return $prices[$this->template] ?? $prices['default'];
    }

    public function toOrderArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author->getUsername(),
            'template' => $this->template,
            'card_title' => $this->card_title,
            'card_subtitle' => $this->card_subtitle,
            'card_body' => $this->card_body,
            'card_image' => $this->card_image,
        ];
    }

    public function getOrderLabel(): string
    {
        return $this->name;
    }
}
