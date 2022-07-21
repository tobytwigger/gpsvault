<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

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
