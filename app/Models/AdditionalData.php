<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'key', 'value'
    ];

    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }


}
