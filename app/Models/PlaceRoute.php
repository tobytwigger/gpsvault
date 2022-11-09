<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\PlaceRoute
 *
 * @property int $id
 * @property int $route_id
 * @property int $place_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute wherePlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlaceRoute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlaceRoute extends Pivot
{
    public $incrementing = true;
}
