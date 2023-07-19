<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    const DELIVERY_OPTION_NEXT_DAY = 'next_day';
    const DELIVERY_OPTION_NEXT_DAY_BY_MIDDAY = 'next_day_by_midday';
    const DELIVERY_OPTION_STANDARD = 'standard';
    const DELIVERY_OPTION_YESTERDAY = 'yesterday'; // For testing command

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_DELAYED = 'delayed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('order')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('order')]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups('order')]
    #[Assert\NotBlank]
    private ?string $deliveryAddress = null;

    #[ORM\Column(length: 255)]
    #[Groups('order')]
    #[Assert\Choice(callback: 'getDeliveryOptions', message: 'Invalid delivery option')]
    private ?string $deliveryOption = null;

    #[ORM\Column(length: 255)]
    #[Groups('order')]
    #[Assert\Choice(callback: 'getStatuses', message: 'Invalid status')]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('order')]
    private ?DateTimeInterface $estimatedDeliveryDate = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('order')]
    #[Assert\NotNull]
    private ?OrderItems $OrderItems = null;

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

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getDeliveryOption(): ?string
    {
        return $this->deliveryOption;
    }

    public function setDeliveryOption(string $deliveryOption): static
    {
        $this->deliveryOption = $deliveryOption;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEstimatedDeliveryDate(): ?DateTimeInterface
    {
        return $this->estimatedDeliveryDate;
    }

    public function setEstimatedDeliveryDate(DateTimeInterface $estimatedDeliveryDate): static
    {
        $this->estimatedDeliveryDate = $estimatedDeliveryDate;

        return $this;
    }

    public function getOrderItems(): ?OrderItems
    {
        return $this->OrderItems;
    }

    public function setOrderItems(OrderItems $OrderItems): static
    {
        $this->OrderItems = $OrderItems;

        return $this;
    }

    static public function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_OUT_FOR_DELIVERY,
            self::STATUS_DELIVERED,
            self::STATUS_DELAYED,
        ];
    }

    static public function getDeliveryOptions(): array
    {
        return [
            self::DELIVERY_OPTION_NEXT_DAY,
            self::DELIVERY_OPTION_NEXT_DAY_BY_MIDDAY,
            self::DELIVERY_OPTION_STANDARD,
            self::DELIVERY_OPTION_YESTERDAY,
        ];
    }
}
