<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Endpoints
{

    // This currently requires a priori knowledge of the endpoints we are expected to receive. While we're propping
    // ourselves up with .env file config, this is appropriate; but when we go fully to eos-mc configuration, this
    // needs to be more generic. For now, we prefer any Redis values (from eos-mc), but if they don't exist, we get
    // the values from our config() based on .env tags.
    public static function GetEndpoints() {

        // SciPlay and Bonusing are special cases, being older services. SciPlay's API is protected
        // with an apikey/apisecret, while Bonusing uses an OAuth2 client id/secret.
        // These keys are provided by EOS-MC along with the target URL; if we've been
        // configured, they can be pulled out of Redis. Otherwise, we depend on the older .env mechanisms
        $sciplay_url = config( 'app.sciplay_url' );
        $sciplay_key = config( 'app.sciplay_api_key' );
        $sciplay_secret = config( 'app.sciplay_api_secret' );

        $sciplay = json_decode( Redis::get( 'endpoint.'.str_slug('SciPlay') ), true);
        if($sciplay && isset($sciplay['url']) && $sciplay['url'] != '')
        { $sciplay_url = $sciplay['url']; }

        if($sciplay && isset($sciplay['api_key']) && $sciplay['api_key'] != '')
        { $sciplay_key = $sciplay['api_key']; }

        if($sciplay && isset($sciplay['api_secret']) && $sciplay['api_secret'] != '')
        { $sciplay_secret = $sciplay['api_secret']; }

        $bonusing_url =  config( 'app.bonusing_url' );
        $bonusing_client_id = config( 'app.bonusing_client_id');
        $bonusing_client_secret = config( 'app.bonusing_client_secret');

        $bonusing = json_decode( Redis::get( 'endpoint. '.str_slug('Bonusing Engine') ), true );
        if($bonusing && isset($bonusing['url']) && $bonusing['url'] != '')
        { $bonusing_url = $bonusing['url']; }
        if($bonusing && isset($bonusing['client_id']) && $bonusing['client_id'] != '')
        { $bonusing_client_id = $bonusing['client_id']; }
        if($bonusing && isset($bonusing['client_secret']) && $bonusing['client_secret'] != '')
        { $bonusing_client_secret = $bonusing['client_secret']; }

        // add these first two services to $endpoints
        $endpoints = [
            ['name' => 'SciPlay', 'url' => $sciplay_url, 'auth' => 'apikey',
                'api_version' => '1', 'api_key' => $sciplay_key, 'api_secret' => $sciplay_secret],
            ['name' => 'Bonusing', 'url' => $bonusing_url, 'auth' => 'oauth',
                'client_id' => $bonusing_client_id, 'client_secret' => $bonusing_client_secret,
                'oauth_token' => null],
        ];

        // all other services will exclusively be configured from eos-mc
        $services = config('app.known_services');
        foreach( $services as $name => $options ) {
            if( ($name != "Bonusing Engine") && $name != "SciPlay" ) {
                $service = json_decode( Redis::get( 'endpoint.'.str_slug($name) ), true );
                $endpoint = ['name' => $name,
                    'url' => $service['url'],
                    'auth' => 'none'];
                if( isset($service['client_id']) && isset($service['client_secret']) ) {
                    $endpoint['auth'] = 'oauth';
                    $endpoint['client_id'] = $service['client_id'];
                    $endpoint['client_secret'] = $service['client_secret'];
                }
                $endpoints[] = $endpoint;
            }
        }


        return $endpoints;
    }

}