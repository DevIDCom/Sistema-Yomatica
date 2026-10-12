<?php
namespace App\Domains\Tickets\Infrastructure\Services;

use Illuminate\Support\Facades\Http;

class GoogleGeofenceService
{
    /**
     * Valida si el técnico está dentro del rango por vialidades reales.
     */
    public static function isWithinDrivingDistance(string $techCoords, float $venueLat, float $venueLng, int $maxMeters = 200): bool
    {
        $apiKey = config('services.google_maps.api_key');

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins'      => $techCoords,
            'destinations' => "{$venueLat},{$venueLng}",
            'mode'         => 'walking',
            'key'          => $apiKey,
        ]);

        $element = $response->json('rows.0.elements.0');

        if (($element['status'] ?? null) !== 'OK') {
            return false;
        }

        $distanceInMeters = $element['distance']['value'];

        return $distanceInMeters <= $maxMeters;
    }
}
