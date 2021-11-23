<?php

namespace App\Models;

use App\Services\ActivityData\ActivityData;
use App\Services\ActivityData\Analysis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use function Illuminate\Events\queueable;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'filepath', 'type', 'distance', 'start_at', 'additional_data', 'linked_to'
    ];

    protected $casts = [
        'distance' => 'float',
        'start_at' => 'datetime',
        'additional_data' => 'array',
        'linked_to' => 'array',
    ];

    public function scopeWhereAdditionalDataContains(Builder $query, string $id, $value)
    {
        $query->where('additional_data', 'LIKE', sprintf('%%"%s":"%s"%%', $id, $value))
            ->orWhere('additional_data', 'LIKE', sprintf('%%"%s":%s%%', $id, $value));
    }

    public function scopeLinkedTo(Builder $query, string $linkedTo)
    {
        $query->where('linked_to', 'LIKE', sprintf('%%%s%%', $linkedTo));
    }

    public function scopeWithoutFile(Builder $query)
    {
        return $query->whereNull('filepath');
    }

    protected static function booted()
    {
        static::created(queueable(function(Activity $activity) {
            if($activity->hasFilepath()) {
                $data = $activity->getActivityData();
                $activity->distance = $data->getDistance();
                $activity->start_at = $data->getStartedAt();
                $activity->save();
            }
        }));
    }

    public function hasFilepath(): bool
    {
        return $this->filepath !== null;
    }

    public function getDataAttribute()
    {
        return $this->getActivityData();
    }

    public function getActivityData(): Analysis
    {
        return ActivityData::analyse($this);
    }
}
