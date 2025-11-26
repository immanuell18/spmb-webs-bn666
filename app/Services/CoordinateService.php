<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoordinateService
{
    public function validateIndonesianCoordinates($lat, $lng)
    {
        // Indonesia bounding box
        $bounds = [
            'lat_min' => -11.0,  // Southernmost point
            'lat_max' => 6.0,    // Northernmost point  
            'lng_min' => 95.0,   // Westernmost point
            'lng_max' => 141.0   // Easternmost point
        ];
        
        return $lat >= $bounds['lat_min'] && $lat <= $bounds['lat_max'] &&
               $lng >= $bounds['lng_min'] && $lng <= $bounds['lng_max'];
    }

    public function geocodeAddress($address)
    {
        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address . ', Indonesia',
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'id',
                'addressdetails' => 1
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                $lat = (float) $data['lat'];
                $lng = (float) $data['lon'];
                
                if ($this->validateIndonesianCoordinates($lat, $lng)) {
                    return [
                        'success' => true,
                        'lat' => $lat,
                        'lng' => $lng,
                        'display_name' => $data['display_name'],
                        'source' => 'GEOCODING'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Alamat tidak ditemukan atau di luar Indonesia'
            ];

        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error geocoding: ' . $e->getMessage()
            ];
        }
    }

    public function reverseGeocode($lat, $lng)
    {
        if (!$this->validateIndonesianCoordinates($lat, $lng)) {
            return [
                'success' => false,
                'message' => 'Koordinat di luar wilayah Indonesia'
            ];
        }

        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $lat,
                'lon' => $lng,
                'format' => 'json',
                'zoom' => 18,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'address' => $data['display_name'] ?? '',
                    'details' => $data['address'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => 'Tidak dapat menemukan alamat untuk koordinat tersebut'
            ];

        } catch (\Exception $e) {
            Log::error('Reverse geocoding error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error reverse geocoding: ' . $e->getMessage()
            ];
        }
    }

    public function getCurrentLocation()
    {
        // This would be called from frontend JavaScript
        // Return default Jakarta coordinates as fallback
        return [
            'lat' => -6.2088,
            'lng' => 106.8456,
            'source' => 'DEFAULT'
        ];
    }

    public function validateCoordinateInput($lat, $lng)
    {
        $errors = [];
        
        if (!is_numeric($lat) || !is_numeric($lng)) {
            $errors[] = 'Koordinat harus berupa angka';
        }
        
        if (!$this->validateIndonesianCoordinates($lat, $lng)) {
            $errors[] = 'Koordinat harus berada dalam wilayah Indonesia';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function formatCoordinates($lat, $lng, $precision = 6)
    {
        return [
            'lat' => round($lat, $precision),
            'lng' => round($lng, $precision),
            'formatted' => round($lat, $precision) . ', ' . round($lng, $precision)
        ];
    }
}