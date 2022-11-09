<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdditionalData
 *
 * @property int $id
 * @property string $additional_data_type
 * @property int $additional_data_id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\AdditionalDataFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereAdditionalDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereAdditionalDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdditionalData whereValue($value)
 * @mixin \Eloquent
 */
class AdditionalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'key', 'value',
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
