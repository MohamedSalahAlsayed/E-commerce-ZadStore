<?php

namespace App\Services\Shipping;

interface ShippingServiceInterface
{
    /**
     * Calculate shipping rate for a specific destination and items.
     */
    public function calculateRate(int $governorateId, array $items): array;

    /**
     * Create a shipment with the provider.
     */
    public function createShipment(array $orderData): array;

    /**
     * Get tracking information.
     */
    public function getTracking(string $trackingNumber): array;
}
