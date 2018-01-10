<?php

namespace App;

use Illuminate\Support\Facades\Redis;
use App\SettingsSchema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Endpoints
{

    /**
     * This currently requires a priori knowledge of the endpoints we are expected to receive. While we're propping
     * ourselves up with .env file config, this is appropriate; but when we go fully to eos-mc configuration, this
     * needs to be more generic. For now, we prefer any Redis values (from eos-mc), but if they don't exist, we get
     * the values from our config() based on .env tags.
     *
     * @return array of arrays (['name','url','auth','api_version','api_secret',
     *    'api_key', 'client_id', 'client_secret', 'oauth_token'])
     */
    public static function GetEndpoints()
    {
        // SciPlay and Bonusing are special cases, being older services. SciPlay's API is protected
        // with an apikey/apisecret, while Bonusing uses an OAuth2 client id/secret.
        // These keys are provided by EOS-MC along with the target URL; if we've been
        // configured, they can be pulled out of Redis. Otherwise, we depend on the older .env mechanisms
        $sciplay_url = config( 'app.sciplay_url' );
        $sciplay_key = config( 'app.sciplay_api_key' );
        $sciplay_secret = config( 'app.sciplay_api_secret' );

        $bonusing_url =  config( 'app.bonusing_url' );
        $bonusing_client_id = config( 'app.bonusing_client_id');
        $bonusing_client_secret = config( 'app.bonusing_client_secret');

        // add these first two services to $endpoints
        $endpoints = [
            ['name' => 'SciPlay', 'url' => $sciplay_url, 'auth' => 'apikey',
                'api_version' => '1', 'api_key' => $sciplay_key, 'api_secret' => $sciplay_secret],
            ['name' => 'Bonusing', 'url' => $bonusing_url, 'auth' => 'oauth',
                'client_id' => $bonusing_client_id, 'client_secret' => $bonusing_client_secret,
                'oauth_token' => null],
        ];

        // all other services will exclusively be configured from eos-mc.
        // if Bonusing or SciPlay are configured, they will replace the above.
        $services = config('app.known_services');
        foreach( $services as $name => $options )
        {
            $service_name = str_slug($name);
            $service = SettingsSchema::fetch('Connections.outbound.'.$service_name);
            if( is_array($service) )
            {
                $endpoint = [
                    'name' => $service['serviceName'],
                    'url' => $service['serviceUrl'],
                    'auth' => isset($service['authentication']) ? $service['authentication'] : 'none' ];
                if (isset($service['clientid']) && isset($service['clientsecret']))
                {
                    $endpoint['client_id'] = $service['clientid'];
                    $endpoint['client_secret'] = $service['clientsecret'];
                }
                if (isset($service['apikey']) && isset($service['apisecret']))
                {
                    $endpoint['api_key'] = $service['apikey'];
                    $endpoint['api_secret'] = $service['apisecret'];
                }
                // delete any duplicate (e.g. replace old SciPlay/Bonusing)
                foreach( $endpoints as $ix => $ep)
                {
                    if( isset($endpoints[$ix]) && ($endpoints[$ix]['name'] == $endpoint['name']) )
                    { unset( $endpoints[$ix] ); }
                }
                $endpoints[] = $endpoint;
            }
        }
        return $endpoints;
    }

    /**
     * Return just the specified endpoint, by name
     *  must match name in config('app.known_services')
     * @param $service_name
     * @return mixed|null -  array['name','url','auth','api_version','api_secret',
     *    'api_key', 'client_id', 'client_secret', 'oauth_token']
     */
    public static function GetEndpoint( $service_name )
    {
        $endpoints = Endpoints::GetEndpoints();
        foreach( $endpoints as $endpoint ) {
            if( $endpoint['name'] == $service_name )
            { return $endpoint; }
        }
        return null;
    }
}