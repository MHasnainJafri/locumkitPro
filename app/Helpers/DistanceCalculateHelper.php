<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class DistanceCalculateHelper
{
    public function getCoordinates(string $zip, ?string $address = null): array
    {
        $query = urlencode($zip);
        if ($address) {
            $query .= "+" . urlencode($address);
        }
        $cacheKey = 'geocode_' . $query;
        try {
            // Check if the response is already cached
            if (Cache::has($cacheKey)) {
                $decodedResult = Cache::get($cacheKey);
            } else {
                $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . $query . ".json?access_token=" . config('app.mapbox_api_key');

                $result = file_get_contents($url);
                $decodedResult = json_decode($result, true);


                if (count($decodedResult["features"]) == 0) {
                    throw new \Exception("Mapbox Geocoding API could not find coordinates for zip code $zip and address $address");
                }

                // Cache the response forever
                Cache::forever($cacheKey, $decodedResult);
            }

            $longitude = $decodedResult["features"][0]["center"][0];
            $latitude = $decodedResult["features"][0]["center"][1];

            return [
                "lat" => $latitude,
                "lng" => $longitude,
            ];
        } catch (\Throwable $e) {
            // Return default coordinates if there's an error
            return [
                "lat" => 51.18291,
                "lng" => -0.63098,
            ];
        }
    }

    public function getLatitude(string $zip, ?string $address = null): float
    {
        $coordinates = $this->getCoordinates($zip, $address);
        return $coordinates['lat'];
    }

    public function getLongitude(string $zip, ?string $address = null): float
    {
        $coordinates = $this->getCoordinates($zip, $address);
        return $coordinates['lng'];
    }

    public function getDistance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = "M"): float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        switch ($unit) {
            case "K":
                return ($miles * 1.609344);
            case "N":
                return ($miles * 0.8684);
            default:
                return $miles;
        }
    }

    public function getDistanceFromAddress(string $address1, string $address2, string $unit = "M"): float
    {
        $result1 = $this->getCoordinates($address1);
        $result2 = $this->getCoordinates($address2);

        return $this->getDistance($result1["lat"], $result1["lng"], $result2["lat"], $result2["lng"], $unit);
    }
}
