<?php

namespace App\Traits;

use App\Models\AdditionalData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HasAdditionalData
{
    public function initializeHasAdditionalData()
    {
        $this->append('additional_data');
    }

    public static function bootHasAdditionalData()
    {
        static::deleting(function ($model) {
            $model->additionalData()->delete();
        });
    }

    public function additionalData()
    {
        return $this->morphMany(AdditionalData::class, 'additional_data');
    }

    public function getAdditionalDataAttribute()
    {
        return $this->getAllAdditionalData();
    }

    public function setAdditionalData(string $key, mixed $value)
    {
        $this->additionalData()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function pushToAdditionalDataArray(string $key, mixed $value)
    {
        $this->additionalData()->create(
            ['key' => $key, 'value' => $value]
        );
    }

    public function getAdditionalData(?string $key = null, mixed $default = null)
    {
        if ($key === null) {
            return $this->getAllAdditionalData();
        }
        if ($this->hasAdditionalData($key)) {
            $result = $this->additionalData()
                ->where('key', $key)
                ->get()
                ->map
                ->value;
            if ($result->count() > 1) {
                return $result;
            }
            if ($result->count() === 1) {
                return $result->first();
            }
        }

        return $default;
    }

    public function hasAdditionalData(string $key): bool
    {
        return $this->additionalData()->where(['key' => $key])->exists();
    }

    public function getAllAdditionalData()
    {
        return $this->additionalData()
            ->get()
            ->groupBy('key')
            ->mapWithKeys(function (Collection $additionalData, string $key) {
                $value = null;
                if ($additionalData->count() === 1) {
                    $value = $additionalData->first()->value;
                } elseif ($additionalData->count() > 1) {
                    $value = $additionalData->map->value;
                }

                return [$key => $value];
            });
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Collection|Collection
     */
    public function getAllNonArrayAdditionalData()
    {
        return $this->additionalData()
            ->get()
            ->groupBy('key')
            ->filter(fn (Collection $datas) => $datas->count() === 1)
            ->mapWithKeys(fn (Collection $additionalData, string $key) => [$key => $additionalData->first()->value]);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Collection|Collection
     */
    public function getAllArrayAdditionalData()
    {
        return $this->additionalData()
            ->get()
            ->groupBy('key')
            ->filter(fn (Collection $datas) => $datas->count() > 1)
            ->mapWithKeys(fn (Collection $additionalData, string $key) => [$key => $additionalData->map->value]);
    }

    public function scopeWhereHasAdditionalData(Builder $query, string $key)
    {
        $query->whereHas('additionalData', fn (Builder $subquery) => $subquery->where('key', $key));
    }

    public function scopeWhereAdditionalData(Builder $query, string $key, $value)
    {
        $query->whereHas(
            'additionalData',
            fn (Builder $subQuery) => $subQuery->where('key', $key)->where('value', serialize($value))
        );
    }
}
