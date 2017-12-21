<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\Endpoints;


class EOSService
{
    public static $transaction_id;
    public static $auth_header;
    public $service_name;
    public $service_url;
    public $auth_type;
    public $client_id;
    public $client_secret;

    public function __construct( $name )
    {
        $this->service_name = $name;
        $endpoints = Endpoints::GetEndpoints();
        foreach( $endpoints as $endpoint ) {
            if( $endpoint['name'] == $name ) {
                $this->service_url = $endpoint['url'];
                $this->auth_type = $endpoint['auth'];
                $this->client_id = $endpoint['client_id'];
                $this->client_secret = $endpoint['client_secret'];
            }
        }
    }

    // todo: 'send' is a generic send stub. What is really needed is
    // a facade for post, put, get, delete corresponding to Guzzle calls.
    // this just shows that we're trying to use the right URL and passing
    // along the TID and SPAT.
    public function send()
    {
        if( ! self::$transaction_id )
        { self::$transaction_id = uniqid(''); }
        if( config( 'app.eos_log_outbound' ) ) {
            $token_value = json_decode(self::$auth_header,true);
            $player = isset($token_value['player']['registrar_id']) ?
                $token_value['player']['registrar_id'] : null;
            $agent = isset($token_value['agent']['agent_id']) ?
                $token_value['agent']['agent_id'] : null;
            Log::info('OUT: '.$this->service_url.' TID:'.self::$transaction_id.
                ($player ? ' Player '.$player : '').
                ($agent ? ' Agent '.$agent : ''));
        }
    }

}

class CheckProcessorService extends EOSService
{
    public function __construct( )
    {
        parent::__construct('Check Processor');
    }
    // basic API send methods are inherited, but here we can
    // add any other custom service methods.
}
