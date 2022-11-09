<?php

namespace App\Services\Dashboard\Database;

use App\Services\Dashboard\WidgetStore;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Services\Dashboard\Database\Widget
 *
 * @property int $id
 * @property string $widget_key
 * @property array $settings
 * @property array $position
 * @property int $dashboard_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Services\Dashboard\Database\Dashboard[] $dashboard
 * @property-read int|null $dashboard_count
 * @method static \Illuminate\Database\Eloquent\Builder|Widget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget query()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereDashboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereWidgetKey($value)
 * @mixin \Eloquent
 */
class Widget extends Model
{

    protected $fillable = [
        'dashboard_id',
        'settings',
        'widget_key',
        'position'
    ];

    protected $casts = [
        'settings' => 'array',
        'position' => 'array'
    ];

    public function dashboard()
    {
        return $this->hasMany(Dashboard::class);
    }

    public function toSchema(): array
    {
        return app(WidgetStore::class)->get($this->widget_key)::create($this->id, $this->settings, $this->position)->toSchema();
    }

}
