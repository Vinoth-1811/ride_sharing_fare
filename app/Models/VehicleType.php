<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleType extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'base_fare',
        'cost_per_km',
    ];

    /**
     * Get the trips for the vehicle type.
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
