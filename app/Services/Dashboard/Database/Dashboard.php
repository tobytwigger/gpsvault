<?php

namespace App\Services\Dashboard\Database;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Services\Dashboard\Database\Dashboard.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $refresh_rate_in_seconds
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Services\Dashboard\Database\Widget[] $widgets
 * @property-read int|null $widgets_count
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereRefreshRateInSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard whereUserId($value)
 * @mixin \Eloquent
 */
class Dashboard extends Model implements \App\Services\Dashboard\Contracts\Dashboard
{
    protected $fillable = [
        'user_id', 'name', 'description', 'refresh_rate_in_seconds',
    ];

    protected $casts = [
        'refresh_rate_in_seconds' => 'float',
    ];

    public function widgets()
    {
        return $this->hasMany(Widget::class);
    }

    public function toSchema(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'refresh_rate' => $this->refresh_rate_in_seconds,
            'widgets' => $this->widgets->map->toSchema(),
        ];
    }
}
