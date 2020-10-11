<?php

namespace App\Http\Middleware;

use App\Helpers\SettingsHelpers;
use App\Models\{Subscribers, Subscriptions};
use Closure;

class RemoveSubscriber
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (file_exists(base_path('.env')) && SettingsHelpers::getSetting('REMOVE_SUBSCRIBER')) {

            $interval = "created_at < NOW() - INTERVAL '" . SettingsHelpers::getSetting('DAYS_FOR_REMOVE_SUBSCRIBER') . "' DAY";

            $subscribers = Subscribers::where('active', 0)->whereRaw($interval);

            if ($subscribers->count() > 0) {
                foreach ($subscribers->get() as $subscriber) {
                    Subscriptions::where('subscriberId',$subscriber->id)->delete();
                }

                $subscribers->delete();
            }
        }

        return $next($request);
    }
}
