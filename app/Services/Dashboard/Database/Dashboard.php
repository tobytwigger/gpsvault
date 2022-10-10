<?php

namespace App\Services\Dashboard\Database;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model implements \App\Services\Dashboard\Contracts\Dashboard
{

    protected $fillable = [
        'user_id', 'name', 'description', 'refresh_rate_in_seconds'
    ];

    protected $casts = [
        'refresh_rate_in_seconds' => 'float'
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
            'widgets' => $this->widgets->map->toSchema()
        ];
    }
}
