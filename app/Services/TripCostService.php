<?php

namespace App\Services;
use App\Models\Vehicle\VehicleType;

class TripCostService
{

    public function calculateTripCost($vehicle_type_id, $estimated_fare)
    {
        $vehicleType = VehicleType::findOrFail($vehicle_type_id);
        $commissionPercentage = $vehicleType->commission_percentage;

        return ($estimated_fare * $commissionPercentage) / 100;
    }

    


}
