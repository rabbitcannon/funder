<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\SettingsSchema;
use App\ApiTraceLogger;
use App\AuthPlayer;
use App\AuthAgent;
use App\EOSService;

class EOSAuthBinding
{
    /**
     * Middleware to decode an EOS custom X-Auth header, which contains
     * a JSON representation of a player, an agent, or both; these will be
     * constructed into AuthPlayer/AuthAgent model objects for injection into
     * the controller method.
     * A registrar_id is required for a Player; an agent_id is required for an Agent.
     *
     * Note that the X-Auth header is not explicitly required by this middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // save any incoming transaction id
        $tid = $request->query('transaction_id');
        EOSService::$transaction_id = $tid;

        $x_auth = $request->header('X-Auth');
        $token_value = json_decode($x_auth,true);
        // save the X-Auth header for relay to other services
        EOSService::$auth_header = $x_auth;

        if (isset($token_value['player']) && isset($token_value['player']['registrar_id'])) {
            $player = new AuthPlayer($token_value['player']);
        } else {
            $player = new AuthPlayer(['registrar_id' => null]);
        }
        $request->route()->setParameter("App\Player", $player);

        if (isset($token_value['agent']) && isset($token_value['agent']['agent_id'])) {
            $agent = new AuthAgent($token_value['agent']);
        } else {
            $agent = new AuthAgent(['agent_id' => null]);
        }
        $request->route()->setParameter("App\Agent", $agent);

        $do_logging = SettingsSchema::fetch('Diagnostics.logInbound');
        if( $do_logging ) {
            $trace = new ApiTraceLogger();
            $trace->info('IN('.$request->method().'): '.$request->path().' TID:'.$tid.
                ($player->registrar_id ? ' Player '.$player->registrar_id : '').
                ($agent->agent_id ? ' Agent '.$agent->agent_id : ''));
        }

        return $next($request);
    }
}
