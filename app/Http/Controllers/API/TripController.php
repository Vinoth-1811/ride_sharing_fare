<?php

namespace App\Http\Controllers\API;

use App\Models\Trip;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TripRepository;
use App\Services\FareCalculatorService;

class TripController extends Controller
{
    protected $fareService;
    protected $tripRepo;

    public function __construct(FareCalculatorService $fareService, TripRepository $tripRepo)
    {
        $this->fareService = $fareService;
        $this->tripRepo = $tripRepo;
    }

    public function calculateFare(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'distance' => 'required|numeric|min:1',
            'requests_per_minute' => 'required|integer|min:0'
        ]);

        $user = User::find($request->user_id);
        $vehicleType = VehicleType::find($request->vehicle_type_id);

        $result = $this->fareService->calculateFare($user, $vehicleType, $request->distance, $request->requests_per_minute);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result, 200);
    }

    public function storeTrip(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'distance' => 'required|numeric|min:1',
            'fare' => 'required|numeric|min:0'
        ]);

        $trip = $this->tripRepo->store($request->only('user_id', 'vehicle_type_id', 'distance', 'fare'));

        return response()->json(['trip' => $trip], 201);
    }

    public function getUsers()
    {
        $users = User::all();
        return response()->json($users);
    }
}
