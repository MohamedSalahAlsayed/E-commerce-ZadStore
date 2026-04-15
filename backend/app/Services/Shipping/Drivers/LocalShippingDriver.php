<?php

namespace App\Services\Shipping\Drivers;

use App\Services\Shipping\ShippingServiceInterface;
use App\Models\Governorate;
use App\Models\ShippingMethod;

class LocalShippingDriver implements ShippingServiceInterface
{
    public function calculateRate(int $governorateId, array $items): array
    {
        $gov = Governorate::with(['zone.methods' => function($q) {
            $q->where('is_active', true);
        }])->find($governorateId);

        if (!$gov || !$gov->zone) {
            return [];
        }

        return $gov->zone->methods->map(function($method) {
            return [
                'id' => $method->id,
                'name_ar' => $method->name_ar,
                'name_en' => $method->name_en,
                'fee' => $method->fee,
                'delivery_time' => $method->delivery_time,
                'driver' => 'internal'
            ];
        })->toArray();
    }

    public function createShipment(array $orderData): array
    {
        // For local shipping, we just return a success with a dummy tracking number
        return [
            'success' => true,
            'tracking_number' => 'ZAD-' . strtoupper(uniqid()),
            'provider' => 'internal'
        ];
    }

    public function getTracking(string $trackingNumber): array
    {
        return [
            'status' => 'pending',
            'history' => []
        ];
    }
}
