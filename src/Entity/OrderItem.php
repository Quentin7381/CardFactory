<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referencedEntityType = null;

    #[ORM\Column(nullable: true)]
    private ?int $referencedEntityId = null;

    #[ORM\Column(length: 1025, nullable: true)]
    private ?string $referencedEntityData = null;

    #[ORM\ManyToOne(inversedBy: 'OrderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $relatedOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getReferencedEntityType(): ?string
    {
        return $this->referencedEntityType;
    }

    public function setReferencedEntityType(?string $referencedEntityType): static
    {
        $this->referencedEntityType = $referencedEntityType;

        return $this;
    }

    public function getReferencedEntityId(): ?int
    {
        return $this->referencedEntityId;
    }

    public function setReferencedEntityId(?int $referencedEntityId): static
    {
        $this->referencedEntityId = $referencedEntityId;

        return $this;
    }

    public function getReferencedEntityData(): ?string
    {
        return $this->referencedEntityData;
    }

    public function setReferencedEntityData(?string $referencedEntityData): static
    {
        $this->referencedEntityData = $referencedEntityData;

        return $this;
    }

    public function getRelatedOrder(): ?Order
    {
        return $this->relatedOrder;
    }

    public function setRelatedOrder(?Order $relatedOrder): static
    {
        $this->relatedOrder = $relatedOrder;

        return $this;
    }

    public function setReferencedEntity(\App\Entity\Interface\OrderableInterface $relatedEntity): static
    {
        $class = get_class($relatedEntity);
        $id = $relatedEntity->getId();
        $data = $relatedEntity->toOrderArray();

        if (empty($id) || (!is_numeric($id) && !is_string($id))) {
            throw new \InvalidArgumentException('The related entity getId() method must return a valid id.');
        }

        if (empty($data) || !is_array($data)) {
            throw new \InvalidArgumentException('The related entity _toArray() method must return a valid array.');
        }

        $this->referencedEntityType = $class;
        $this->referencedEntityId = $id;
        $this->referencedEntityData = json_encode($data);

        return $this;
    }
}
