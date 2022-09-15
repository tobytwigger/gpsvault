<?php

namespace App\Http\Controllers\Pages\Route\Planner\Tools;

use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\Valhalla\Valhalla;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NewWaypointLocatorController
{

    const SAMPLE_SIZE = 100;

    public function __invoke(Request $request)
    {
        $request->validate([
            'geojson' => 'required|array|min:2',
            'geojson.*.lat' => 'required|numeric|min:-90|max:90',
            'geojson.*.lng' => 'required|numeric|min:-180|max:180',
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
        ]);

        $fullLinestring = collect($request->input('geojson'))
            ->map(fn(array $coords) => ['lat' => $coords['lat'], 'lon' => $coords['lng']]);

        // Try putting it at index 1 (2nd pos).
        $lowestCostIndex = null;
        $lowestCost = null;

        for($i = 1; $i <= $fullLinestring->count() - 1; $i++) {
            $indexes = clone $fullLinestring;
            $indexes->splice($i, 0, [['lat' => $request->input('lat'), 'lon' => $request->input('lng')]]);
            $response = (new Valhalla())->route($indexes->all());
            $cost = $response['trip']['legs'][0]['summary']['cost'];
            if($lowestCost === null || $lowestCost > $cost) {
                $lowestCost = $cost;
                $lowestCostIndex = $i;
            }
        }

        return [
            'index' => $lowestCostIndex
        ];
    }

}
