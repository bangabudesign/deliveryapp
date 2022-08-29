<?php
namespace App\Helpers;

class Delivery {
    
    public static function getFee($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lngFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lngTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
        $distance = round($angle * $earthRadius);


        $price_1 = ($distance > 0) ? 10000 : 10000; $distance =  ($distance > 0)? $distance - 1 :  0;
        $price_2 = ($distance - 1) > 0 ? 10000 : ($distance * 10000); $distance = ($distance-1)>0 ? $distance - 1 : 0;
        $price_3 = ($distance > 0) ? ($distance * 2000) : 0;

        $delivery_fee = ($price_1 + $price_2 + $price_3);

        return $delivery_fee;
    }
}