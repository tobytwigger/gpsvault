<?php

namespace App\Http\Controllers\Pages\Settings;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use Settings\Rules\ArrayKeyIsValidSettingKeyRule;
use Settings\Rules\SettingValueIsValidRule;
use Settings\Setting;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/Index', [
            'sessions' => $this->sessions($request)->all(),
            'stravaClients' => Auth::user()->can('manage-global-settings') ? StravaClient::public()->enabled()->get() : []
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            '*' => [
                app(ArrayKeyIsValidSettingKeyRule::class),
                app(SettingValueIsValidRule::class),
            ],
        ]);

        foreach ($request->all() as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.index');
    }

    /**
     * Get the current sessions.
     *
     * @param Request $request
     * @return Collection
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
     * @return Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent(), function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
