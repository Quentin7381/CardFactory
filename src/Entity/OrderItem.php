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
    private ?string $Label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ReferencedEntityType = null;

    #[ORM\Column(nullable: true)]
    private ?int $ReferencedEntityId = null;

    #[ORM\Column(length: 1025, nullable: true)]
    private ?string $ReferencedEntityData = null;

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
        return $this->Label;
    }

    public function setLabel(string $Label): static
    {
        $this->Label = $Label;

        return $this;
    }

    public function getReferencedEntityType(): ?string
    {
        return $this->ReferencedEntityType;
    }

    public function setReferencedEntityType(?string $ReferencedEntityType): static
    {
        $this->ReferencedEntityType = $ReferencedEntityType;

        return $this;
    }

    public function getReferencedEntityId(): ?int
    {
        return $this->ReferencedEntityId;
    }

    public function setReferencedEntityId(?int $ReferencedEntityId): static
    {
        $this->ReferencedEntityId = $ReferencedEntityId;

        return $this;
    }

    public function getReferencedEntityData(): ?string
    {
        return $this->ReferencedEntityData;
    }

    public function setReferencedEntityData(?string $ReferencedEntityData): static
    {
        $this->ReferencedEntityData = $ReferencedEntityData;

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

    public function setReferencedEntity(object $relatedEntity): static
    {
        $class = get_class($relatedEntity);
        $id = $relatedEntity->getId();
        $data = $relatedEntity->serialize();

        if (empty($id) || (!is_numeric($id) && !is_string($id))) {
            throw new \InvalidArgumentException('The related entity getId() method must return a valid id.');
        }

        if (empty($data) || !is_array($data)) {
            throw new \InvalidArgumentException('The related entity serialize() method must return a valid array.');
        }

        $this->ReferencedEntityType = $class;
        $this->ReferencedEntityId = $id;
        $this->ReferencedEntityData = json_encode($data);

        return $this;
    }
}
