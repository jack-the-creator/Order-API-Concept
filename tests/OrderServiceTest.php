<?php

use App\Entity\Order;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderServiceTest extends KernelTestCase
{
    private OrderService $orderService;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->orderService = $container->get(OrderService::class);
    }

    public function testIsValidStatus(): void
    {
        foreach (Order::getStatuses() as $status) {
            $this->assertTrue($this->orderService->isValidStatus($status));
        }

        $this->assertFalse($this->orderService->isValidStatus("TEST FALSE STATUS"));
    }

    public function testIsValidDeliveryOption(): void
    {
        foreach (Order::getDeliveryOptions() as $deliveryOption) {
            $this->assertTrue($this->orderService->isValidDeliveryOption($deliveryOption));
        }

        $this->assertFalse($this->orderService->isValidDeliveryOption("TEST FALSE DELIVERY OPTION"));
    }

    public function testGetEstimatedDeliveryDate(): void
    {
        $this->assertEquals(
            new DateTime('+3 days 22:00'),
            $this->orderService->getEstimatedDeliveryDate(Order::DELIVERY_OPTION_STANDARD)
        );

        $this->assertEquals(
            new DateTime('+1 day noon'),
            $this->orderService->getEstimatedDeliveryDate(Order::DELIVERY_OPTION_NEXT_DAY_BY_MIDDAY)
        );

        $this->assertEquals(
            new DateTime('+1 day 22:00'),
            $this->orderService->getEstimatedDeliveryDate(Order::DELIVERY_OPTION_NEXT_DAY)
        );

        $this->assertEquals(
            new DateTime('-1 day 08:00'),
            $this->orderService->getEstimatedDeliveryDate(Order::DELIVERY_OPTION_YESTERDAY)
        );

        $this->assertNull($this->orderService->getEstimatedDeliveryDate("INVALID OPTION"));
    }
}