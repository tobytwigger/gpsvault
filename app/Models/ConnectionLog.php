<?php

namespace App\Models;

use App\Integrations\Integration;
use App\Integrations\Strava\Models\StravaClient;
use App\Traits\HasAdditionalData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ConnectionLog extends Model
{
    use HasFactory, HasAdditionalData;

    /**
     * Represents a successful connection
     *
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * Information that can be used for debugging
     *
     * @var string
     */
    const DEBUG = 'debug';

    /**
     * Information that indicates action may need to be taken
     *
     * @var string
     */
    const WARNING = 'warning';

    /**
     * Information that needs no action
     *
     * @var string
     */
    const INFO = 'info';

    /**
     * Information that indicates something went wrong
     *
     * @var string
     */
    const ERROR = 'error';

    protected $fillable = [
        'type',
        'log',
        'user_id',
        'integration'
    ];

    public function scopeForIntegration(Builder $query, Integration $integration)
    {
        $query->where('integration', $integration->id());
    }

    public function scopeForCurrentUser(Builder $query)
    {
        $query->where('user_id', Auth::id());
    }

}
