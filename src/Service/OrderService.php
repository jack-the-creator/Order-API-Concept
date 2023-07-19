<?php

namespace App\Service;

use App\Entity\Order;
use DateTime;

class OrderService
{
    public function isValidStatus(string $status): bool
    {
        $statuses = Order::getStatuses();

        return in_array($status, $statuses);
    }

    public function isValidDeliveryOption(string $option): bool
    {
        $options = Order::getDeliveryOptions();

        return in_array($option, $options);
    }

    public function getEstimatedDeliveryDate(string $deliveryOption): ?DateTime
    {
        $date = new DateTime();

        if (!$this->isValidDeliveryOption($deliveryOption)) {
            return null;
        }

        switch ($deliveryOption) {
            case Order::DELIVERY_OPTION_NEXT_DAY_BY_MIDDAY:
                $date->modify('tomorrow noon');
                break;
            case Order::DELIVERY_OPTION_NEXT_DAY:
                $date->modify('tomorrow 22:00');
                break;
            case Order::DELIVERY_OPTION_STANDARD:
                $date->modify('+3 days 22:00');
                break;
            case Order::DELIVERY_OPTION_YESTERDAY:
                $date->modify('-1 day 08:00');
        }

        return $date;
    }
}