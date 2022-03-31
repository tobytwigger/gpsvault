<?php

namespace App\Http\Middleware;

use App\Settings\BruitAPIKey;
use App\Settings\DarkMode;
use App\Settings\StatsOrder;
use App\Settings\StravaClient;
use App\Settings\UnitSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
//            'settings' => fn() => [
//                'unit_system' => UnitSystem::getValue(),
//                'dark_mode' => DarkMode::getValue(),
//                'strava_client_id' => (Auth::check() && Auth::user()->can('manage-global-settings') ? StravaClient::getValue() : null),
//                'stats_order_preference' => StatsOrder::getValue(),
//                'bruit_api_key' => BruitAPIKey::getValue()
//            ],
            'permissions' => Auth::check() ? Auth::user()->getDirectPermissions()->pluck('name') : []
        ]);
    }
}
