<?php

namespace App\Services\Dashboard\Database;

use App\Services\Dashboard\WidgetStore;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{

    protected $fillable = [
        'dashboard_id',
        'settings',
        'widget_key'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function dashboard()
    {
        return $this->hasMany(Dashboard::class);
    }

    public function toSchema(): array
    {
        return app(WidgetStore::class)->get($this->widget_key)::create($this->settings)->toSchema();
    }

}
