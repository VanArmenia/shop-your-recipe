<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Region;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateRegionsFromRestCountries extends Command
{
    protected $signature = 'regions:update';
    protected $description = 'Update regions data using RestCountries API';

    public function handle()
    {
        // Fetch countries data from RestCountries API
        $response = Http::get('https://restcountries.com/v3.1/all');

        if ($response->failed()) {
            $this->error("Failed to fetch data from RestCountries API");
            return;
        }

        $countries = $response->json();
        $updatedCount = 0;

        foreach ($countries as $country) {
            // Find existing region by name or country code
            $region = Region::where('name', $country['name']['common'])->first();

            if ($region) {
                // Try fetching bounding box with retries
                $mapUrl = $this->getBoundingBoxForCountry($country['name']['common'], $country['latlng']);

                if ($mapUrl) {
                    // Update the region data
                    $region->update([
                        'latitude' => $country['latlng'][0] ?? $region->latitude,
                        'longitude' => $country['latlng'][1] ?? $region->longitude,
                        'map_url' => $mapUrl, // Update map URL
                    ]);

                    $updatedCount++;
                } else {
                    $this->error("Failed to fetch bounding box for {$country['name']['common']}");
                }
            }
        }

        $this->info("$updatedCount regions updated successfully.");
    }

    /**
     * Get bounding box URL for the country, retrying a few times if necessary.
     *
     * @param string $countryName
     * @param array $latlng
     * @return string|null
     */
    private function getBoundingBoxForCountry($countryName, $latlng)
    {
        $retryCount = 0;
        $maxRetries = 3;

        while ($retryCount < $maxRetries) {
            // Fetch bounding box data from Nominatim API
            $geocodeUrl = "https://nominatim.openstreetmap.org/search?country=" . urlencode($countryName) . "&format=json&addressdetails=1";
            $geocodeResponse = Http::withHeaders([
                'User-Agent' => 'RecipesLocal/1.0 (tig.sis.ayas@gmail.com)'
            ])->get($geocodeUrl);

            // Debugging with echo for request and response
            echo "Nominatim Request URL: $geocodeUrl\n";
            echo "Nominatim Response Status: " . $geocodeResponse->status() . "\n"; // Print HTTP status code
            echo "Nominatim Response: " . $geocodeResponse->body() . "\n"; // Print response body

            if ($geocodeResponse->successful()) {
                $geocodeData = $geocodeResponse->json();

                // Ensure bounding box data exists
                if (isset($geocodeData[0]['boundingbox']) && count($geocodeData[0]['boundingbox']) >= 4) {
                    $minLat = $geocodeData[0]['boundingbox'][0];
                    $maxLat = $geocodeData[0]['boundingbox'][1];
                    $minLon = $geocodeData[0]['boundingbox'][2];
                    $maxLon = $geocodeData[0]['boundingbox'][3];

                    // Generate the map URL using the bounding box
                    return 'https://www.openstreetmap.org/export/embed.html?bbox=' .
                        $minLon . ',' . $minLat . ',' . $maxLon . ',' . $maxLat . '&layer=mapnik';
                } else {
                    // If no bounding box data is available, fallback to lat/lng for a centered map
                    return 'https://www.openstreetmap.org/#map=10/' . $latlng[0] . '/' . $latlng[1];
                }
            }

            $retryCount++;

            // If the request fails, log the error and retry with a delay
            if ($retryCount < $maxRetries) {
                echo "Failed to fetch bounding box for $countryName, retrying... ($retryCount)\n";
                sleep(2); // Sleep 2 seconds before retrying
            }
        }

        return null;
    }
}
