<?php

namespace App\Services;

use App\Models\User;
use App\Models\VehicleType;

class FareCalculatorService
{
    public function calculateFare(User $user, VehicleType $vehicleType, $distance, $requests_per_minute)
    {
        // Base fare and cost per km logic
        $baseFare = $this->getBaseFare($vehicleType);
        $costPerKm = $this->getCostPerKm($vehicleType);

        // Surge pricing logic
        $surgeMultiplier = ($requests_per_minute > 100) ? 1.5 : 1;  // Surge if more than 100 requests per minute

        // Calculate total fare
        $fare = $baseFare + ($costPerKm * $distance);

        // Apply surge pricing
        $fare *= $surgeMultiplier;

        // Apply discount for first-time users
        if ($user->trips()->count() === 0) {
            $fare *= 0.9;  // 10% discount for first-time users
        }

        // Maximum distance check
        if ($distance > 500) {
            return 'Distance exceeds maximum limit of 500 km.';
        }

        return round($fare, 2); // Return the calculated fare
    }

    // Helper function to get base fare based on vehicle type
    private function getBaseFare(VehicleType $vehicleType)
    {
        switch ($vehicleType->id) {
            case 1: return 5; // Economy
            case 2: return 8; // Standard
            case 3: return 12; // Luxury
            default: return 0;
        }
    }

    // Helper function to get cost per km based on vehicle type
    private function getCostPerKm(VehicleType $vehicleType)
    {
        switch ($vehicleType->id) {
            case 1: return 1; // Economy
            case 2: return 1.5; // Standard
            case 3: return 2; // Luxury
            default: return 0;
        }
    }
}

