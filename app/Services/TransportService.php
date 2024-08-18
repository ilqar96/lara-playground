<?php

namespace App\Services;

class TransportService
{
    public static function calcTransportPrice($addresses,$vehicle)
    {
        $totalDistance = self::calculateDistance($addresses);

        //calculate vehicle price
        return self::calcVehiclePrice($vehicle,$totalDistance);
    }


    public static function calcVehiclePrice($vehicle,$distance)
    {
        $totalPrice = self::roundFloatValue($distance * $vehicle->cost_km);

        //check if price greater than minimum amount
        if ($totalPrice < $vehicle->minimum){
            $totalPrice = $vehicle->minimum;
        }

        return $totalPrice;
    }

    public static function calculateDistance($addresses)
    {
        $googleMapsService = new GoogleMapsService();

        $totalDistance = 0;
        for ($i = 0; $i < count($addresses) - 1; $i++) {
            $origin = self::formatAddress($addresses[$i]);
            $destination = self::formatAddress($addresses[$i + 1]);

            $totalDistance += $googleMapsService->getDistance($origin, $destination);
        }

        return $totalDistance;
    }

    public static function formatAddress($address)
    {
        return urlencode("{$address['city']}, {$address['zip']}, {$address['country']}");
    }

    public static function roundFloatValue($price, $decimal = 2, $round = 2)
    {
        $decimalPoint = '%0.' . $decimal . 'f';

        return (float)sprintf($decimalPoint, round($price, $round));
    }
}
