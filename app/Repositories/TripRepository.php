<?php

namespace App\Repositories;

use App\Models\Trip;

class TripRepository
{
    public function store($data)
    {
        return Trip::create($data);
    }

    public function all()
    {
        return Trip::with('user', 'vehicleType')->get();  // Fetch trips with relationships
    }
}

