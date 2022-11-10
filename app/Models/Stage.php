<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Stage
 *
 * @property int $id
 * @property int $stage_number
 * @property string|null $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $date
 * @property bool $is_rest_day
 * @property int $tour_id
 * @property int|null $route_id
 * @property int|null $activity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity|null $activity
 * @property-read \App\Models\Route|null $route
 * @property-read \App\Models\Tour $tour
 * @method static \Database\Factories\StageFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stage ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Stage query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereIsRestDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereStageNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereTourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stage extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $fillable = [
        'name', 'description', 'date', 'is_rest_day', 'tour_id', 'route_id', 'activity_id', 'stage_number',
    ];

    protected $sortable = [
        'order_column_name' => 'stage_number',
        'sort_when_creating' => true,
    ];

    protected $casts = [
        'stage_number' => 'integer',
        'date' => 'date',
        'is_rest_day' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleted(function (Stage $stage) {
            static::setNewOrder(
                $stage->buildSortQuery()->ordered()->pluck($stage->getKeyName())
            );
        });

        static::updated(function (Stage $stage) {
            static::setNewOrder(
                $stage->buildSortQuery()->ordered()->pluck($stage->getKeyName())
            );
        });

        static::saved(function (Stage $stage) {
            static::setNewOrder(
                $stage->buildSortQuery()->ordered()->pluck($stage->getKeyName())
            );
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

//    public function routePath()
//    {
//        return $this->hasManyThrough(RoutePath::class, Route::class);
//    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function buildSortQuery()
    {
        return static::query()->where('tour_id', $this->tour_id);
    }

    public function setStageNumber(int $stageNumber)
    {
        $baseOrder = $this->buildSortQuery()->ordered()->where('id', '!=', $this->id)->pluck($this->getKeyName());
        $baseOrder->splice($stageNumber - 1, 0, $this->id);
        static::setNewOrder($baseOrder);
    }
}
