<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoutingRequest extends FormRequest
{

    public function rules()
    {
        return [
            'use_hills' => 'sometimes|nullable|numeric|min:0|max:1',
            'use_roads' => 'sometimes|nullable|numeric|min:0|max:1',
            'waypoints' => 'required|array|min:2|max:250',
            'waypoints.*.location' => 'required|array|min:2|max:2',
            'waypoints.*.location.lat' => 'required|numeric|min:-90|max:90',
            'waypoints.*.location.lng' => 'required|numeric|min:-180|max:180',
        ];
    }

}
