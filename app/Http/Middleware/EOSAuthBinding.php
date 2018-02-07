<?php

namespace App\Http\Middleware;

use Closure;
use App\Setting;
use App\ApiTraceLogger;
use App\AuthAgent;
use App\EOSService;

class EOSAuthBinding
{
    /**
     * Middleware to decode an EOS custom X-Auth-Spat header, which contains
     * a JSON representation of a player, an agent, or both; these will be
     * constructed into AuthPlayer/AuthAgent model objects for injection into
     * the controller method.
     * A registrar_id is required for a Player; an agent_id is required for an Agent.
     *
     * Note that the X-Auth-Spat header is not explicitly required by this middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // save any incoming correlation id
        $cid = $request->query('correlation_id');
        EOSService::$correlation_id = $cid;

        $x_auth = $request->header('X-Auth-Spat');
        $token_value = json_decode($x_auth,true);
        // save the X-Auth-Spat header for relay to other services
        EOSService::$auth_header = $x_auth;

        if (isset($token_value['agent']) && isset($token_value['agent']['agent_id'])) {
            $agent = new AuthAgent($token_value['agent']);
        } else {
            $agent = new AuthAgent(['agent_id' => null]);
        }

        $do_logging = Setting::get('eos.diagnostics.logInbound');
        if( $do_logging ) {
            $trace = new ApiTraceLogger();
            $trace->info('IN('.$request->method().'): '.$request->path().' CID:'.$cid.
                ($agent->agent_id ? ' Agent '.$agent->agent_id : ''));
        }

        return $next($request);
    }
}
