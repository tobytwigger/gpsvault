<?php

namespace App\Http\Controllers\Pages\Settings;

use App\Http\Controllers\Controller;
use App\Settings\DarkMode;
use App\Settings\StatsOrder;
use App\Settings\StravaClient;
use App\Settings\UnitSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use Settings\Setting;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/Index', [
            'sessions' => $this->sessions($request)->all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_system' => 'sometimes|' . Setting::getSettingByKey(UnitSystem::class)->rules(),
            'dark_mode' => 'sometimes|' . Setting::getSettingByKey(DarkMode::class)->rules(),
            'strava_client_id' => 'sometimes|' . Setting::getSettingByKey(StravaClient::class)->rules(),
            'stats_order' => 'sometimes|' . Setting::getSettingByKey(StatsOrder::class)->rules(),
            'stats_order.*' => 'string|in:php,strava'
        ]);

        if($request->has('unit_system')) {
            UnitSystem::setValue($request->input('unit_system'));
        }

        if($request->has('dark_mode')) {
            DarkMode::setValue($request->input('dark_mode'));
        }

        if($request->has('strava_client_id') && Auth::user()->can('manage-global-settings')) {
            StravaClient::setValue($request->input('strava_client_id'));
        }

        if($request->has('stats_order')) {
            StatsOrder::setValue($request->input('stats_order'));
        }

        return redirect()->route('settings.index');
    }

    /**
     * Get the current sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    protected function sessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
