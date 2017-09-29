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

        // SciPlay and Bonusing are given as examples here. SciPlay's API is protected
        // with an apikey/apisecret, while Bonusing uses an OAuth2 client id/secret.
        // These keys are provided by EOS-MC along with the target URL; if we've been
        // configured, they can be pulled out of Redis. Otherwise, we depend on the .env
        $sciplay_url = config( 'app.sciplay_url' );
        $sciplay_key = config( 'app.sciplay_api_key' );
        $sciplay_secret = config( 'app.sciplay_api_secret' );

        $sciplay = json_decode( Redis::get( 'SciPlay' ), true);
        if($sciplay && isset($sciplay['url']) && $sciplay['url'] != '')
        { $sciplay_url = $sciplay['url']; }

        if($sciplay && isset($sciplay['api_key']) && $sciplay['api_key'] != '')
        { $sciplay_key = $sciplay['api_key']; }

        if($sciplay && isset($sciplay['api_secret']) && $sciplay['api_secret'] != '')
        { $sciplay_secret = $sciplay['api_secret']; }

        $bonusing_url =  config( 'app.bonusing_url' );
        $bonusing_client_id = config( 'app.bonusing_client_id');
        $bonusing_client_secret = config( 'app.bonusing_client_secret');

        $bonusing = json_decode( Redis::get( 'Bonusing Engine' ), true );
        if($bonusing && isset($bonusing['url']) && $bonusing['url'] != '')
        { $bonusing_url = $bonusing['url']; }
        if($bonusing && isset($bonusing['client_id']) && $bonusing['client_id'] != '')
        { $bonusing_client_id = $bonusing['client_id']; }
        if($bonusing && isset($bonusing['client_secret']) && $bonusing['client_secret'] != '')
        { $bonusing_client_secret = $bonusing['client_secret']; }

        // roll up and return our best knowledge of URL endpoints, from either .env or Redis
        $endpoints = [
            ['name' => 'SciPlay', 'url' => $sciplay_url, 'auth' => 'apikey',
                'api_version' => '1', 'api_key' => $sciplay_key, 'api_secret' => $sciplay_secret],
            ['name' => 'Bonusing', 'url' => $bonusing_url, 'auth' => 'oauth',
                'client_id' => $bonusing_client_id, 'client_secret' => $bonusing_client_secret,
                'oauth_token' => null],
        ];

        return $endpoints;
    }

}