<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculatePriceRequest;
use App\Http\Resources\VehiclePriceResource;
use App\Models\VehicleType;
use App\Services\TransportService;
use Exception;

class TransportController extends Controller
{
    public function calculatePrice(CalculatePriceRequest $request)
    {
        $validated = $request->validated();
        $addresses = $validated['addresses'];

        //find vehicles
        $vehicles = VehicleType::all();

        $res = [];

        try
        {
            foreach ($vehicles as $vehicle){
                $currPrice = TransportService::calcTransportPrice($addresses,$vehicle);

                $res[] = [
                    'vehicle_type' => $vehicle->number,
                    'price' => $currPrice,
                ];
            }

            return VehiclePriceResource::collection($res)->response()->setStatusCode(200);

        }catch (Exception $e) {
            // Return error response if there was an issue calculating the price
            return response()->json([
                'error' => 'Failed to calculate transport price',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
