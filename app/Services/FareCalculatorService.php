<?php

namespace App\Services;

use App\Models\User;
use App\Models\VehicleType;

class FareCalculatorService
{
    public function calculateFare(User $user, VehicleType $vehicleType, $distance, $requests_per_minute)
    {
        $baseFare = $this->getBaseFare($vehicleType);
        $costPerKm = $this->getCostPerKm($vehicleType);
        $surgeMultiplier = ($requests_per_minute > 100) ? 1.5 : 1;
        $hasDiscount = $user->trips()->count() === 0;

        if ($distance > 500) {
            return ['error' => 'Distance exceeds maximum limit of 500 km.'];
        }

        $subtotal = $baseFare + ($costPerKm * $distance);
        $fare = $subtotal * $surgeMultiplier;

        if ($hasDiscount) {
            $fare *= 0.9;
        }

        return [
            'fare' => round($fare, 2),
            'base_fare' => $baseFare,
            'cost_per_km' => $costPerKm,
            'surge_multiplier' => $surgeMultiplier,
            'has_discount' => $hasDiscount,
            'subtotal' => round($subtotal, 2)
        ];
    }

    private function getBaseFare(VehicleType $vehicleType)
    {
        switch ($vehicleType->id) {
            case 1: return 5;
            case 2: return 8;
            case 3: return 12;
            default: return 0;
        }
    }

    private function getCostPerKm(VehicleType $vehicleType)
    {
        switch ($vehicleType->id) {
            case 1: return 1;
            case 2: return 1.5;
            case 3: return 2;
            default: return 0;
        }
    }
}
