<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlannerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'geojson' => 'required|string',
            'elevation' => 'required|array',
            'elevation.*' => 'required|numeric',
            'waypoints' => 'sometimes|array',
            'waypoints.*' => 'required|array',
            'waypoints.*.id' => 'sometimes|nullable|numeric|exists:waypoints,id',
            'waypoints.*.lat' => 'required|numeric|min:-90|max:90',
            'waypoints.*.lng' => 'required|numeric|min:-180|max:180',
            'waypoints.*.place_id' => 'sometimes|nullable|numeric|exists:places,id',
            'waypoionts.*.name' => 'sometimes|nullable|string|min:1|max:255',
            'waypoionts.*.notes' => 'sometimes|nullable|string|min:1|max:65535',
            'distance' => 'sometimes|nullable|numeric|min:0',
            'elevation_gain' => 'sometimes|nullable|numeric',
            'duration' => 'sometimes|nullable|numeric|min:0',
            'name' => 'sometimes|nullable|string|min:1|max:255',
            'settings' => 'array',
            'settings.use_hills' => 'required|numeric|min:0|max:1',
            'settings.use_roads' => 'required|numeric|min:0|max:1',
        ];
    }
}
