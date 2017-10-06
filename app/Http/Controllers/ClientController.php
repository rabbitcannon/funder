<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Storage;
use Predis\ClientException;

// this controller, or some variant of it, is included in any EOS service to allow push
// of endpoint configuration to the service. We can configure peer service endpoints
// either in our .env file (puppet-generated) or by receiving an /api/configure post
// from EOS-MC. The latter is the preferred method.
class ClientController extends Controller
{

    private static $service_name = 'Boogers'; // the official name of my service

    /**
     * @SWG\Post(
     *   path="/configure",
     *   summary="REQUIRED API FOR SERVICES: Accept configuration push from eos-mc service",
     *   operationId="acceptConfiguration",
     *   tags={"eos"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Service Configuration",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/ServiceConfig")
     *   ),
     *   @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(ref="#/definitions/ServiceConfigResponse")),
     *   @SWG\Response(response=500, description="System error",
     *     @SWG\Schema(ref="#/definitions/ServiceConfigResponse"))
     *  )
     **/
    // accept an endpoints configuration set from EOS-MC. Put each endpoint into Redis, keyed by
    // the target service name.
    // Some services need keys/secrets to access their API's. SciPlay uses an api_key and
    // api_secret to hash content. Bonusing uses an OAuth2 client_id and client_secret.
    // These are delivered by EOS-MC along with the endpoint; it's up to you to use them
    // appropriately.
    // See the 'Endpoints' model for a mechanism to obtain the current endpoint configuration
    // on demand.
    // NOTE: for now, this configuration system is separate from the /settings API.
    // In the future we may integrate these mechanisms
    public function configure( Request $request )
    {
        $service = $request->all();
        if ( $service['name'] == self::$service_name ) {
        // found our own configuration. Pick out some fields.
            if ( !isset($service['outbound']) || !is_array($service['outbound']) ) {
                return response()->json( ['status' => 'error', 'message' => 'No connections'], 500 );
            }
            $urls = [];
            $config = "";
            $status = "ok";
            foreach ( $service['outbound'] as $connection ) {
                // we always get url, we might also get api_key and/or api_secret
                $redis_array = [ 'url' => $connection['url'] ];
                if( isset($connection[ 'api_key' ]) )
                { $redis_array[ 'api_key' ] = $connection[ 'api_key' ]; }
                if( isset($connection['api_secret']) )
                { $redis_array[ 'api_secret' ] = $connection[ 'api_secret' ]; }
                if( isset($connection[ 'client_id' ]) )
                { $redis_array[ 'client_id' ] = $connection[ 'client_id' ]; }
                if( isset($connection['client_secret']) )
                { $redis_array[ 'client_secret' ] = $connection[ 'client_secret' ]; }

                Redis::set( $connection['name'], json_encode( $redis_array ) );
                $config .= $connection['name'] . ' as ' . $connection['url'] . "; ";
            }


            Log::info( 'Service configured: '.$config );

            return response()->json( ['status' => $status, 'config' => $config] );
        }
        return response()->json( ['status' => 'error', 'message' => 'Not my config, expecting '.self::$service_name], 500 );
    }

    /**
     * @SWG\Get(
     *   path="/schema",
     *   summary="REQUIRED API FOR SERVICES: Return settings pack schema",
     *   operationId="settingsSchema",
     *   tags={"eos"},
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function settingsSchema(Request $request)
    {
        $schemafile = file_get_contents('../config/settings.schema.json');
        $schema = json_decode($schemafile);
        return response()->json($schema);
    }

    /**
     * @SWG\Get(
     *   path="/settings",
     *   summary="REQUIRED API FOR SERVICES: Return settings",
     *   operationId="getSettings",
     *   tags={"eos"},
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function getSettings(Request $request)
    {
        $settings = [];
        $settingsJson = Redis::get( 'settings' );
        if( $settingsJson ) {
            $settings = json_decode( $settingsJson, true );
        }
        return response()->json( $settings );
    }

    /**
     * @SWG\Post(
     *   path="/settings",
     *   summary="REQUIRED API FOR SERVICES: Accept settings",
     *   operationId="postSettings",
     *   tags={"eos"},
     * @SWG\Parameter(
     *   in="body",
     *   name="body",
     *   description="Settings JSON Data",
     *   required=true,
     *   @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function postSettings(Request $request)
    {
        // get our current settings for comparison
        $settings = [];
        $settingsJson = Redis::get( 'settings' );
        if( $settingsJson ) {
            $settings = json_decode( $settingsJson, true );
        }
        // decode the new settings
        try
        { $new_settings = json_decode( $request->getContent(), true ); }
        catch( \Exception $e )
        { return response()->json( ['message' => 'Bad JSON detected, not updated'], 500 ); }

       // $changes = $this->array_diff_assoc_recursive( $settings, $new_settings );

        $settingsJson = json_encode( $new_settings );
        Redis::set( 'settings', $settingsJson );

        return response()->json( ['changes' => []] );
    }

    private function array_diff_assoc_recursive($array1, $array2)
    {
        foreach($array1 as $key => $value){
            if(is_array($value)){
                if(!isset($array2[$key]))
                { $difference[$key] = $value; }
                elseif(!is_array($array2[$key]))
                { $difference[$key] = $value;}
                else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if($new_diff != FALSE)
                    { $difference[$key] = $new_diff;}
                }
            }
            elseif((!isset($array2[$key]) || $array2[$key] != $value) && !($array2[$key]===null && $value===null))
            { $difference[$key] = $value;}
        }
        return !isset($difference) ? [] : $difference;
    }

    /**
     * @SWG\Delete(
     *   path="/settings",
     *   summary="REQUIRED API FOR SERVICES: Delete settings",
     *   operationId="deleteSettings",
     *   tags={"eos"},
     * @SWG\Response(response=200, description="successful"),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function deleteSettings(Request $request)
    {
        Redis::del( 'settings' );
        return response()->json( ['message' =>"Settings deleted."] );
    }

}


/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"name","inbound","outbound"}, type="object", @SWG\Xml(name="ServiceConfig"))
 * @SWG\Property(format="string", property="name", example="Bonusing Manager", description="Name of the service")
 * @SWG\Property(type="array", property="inbound", description="All known connections to this service", @SWG\Items(ref="#/definitions/ConnectionConfig"))
 * @SWG\Property(type="array", property="outbound", description="All known connections from this service", @SWG\Items(ref="#/definitions/ConnectionConfig"))
 **/
class ServiceConfig {}
/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"status"}, type="object", @SWG\Xml(name="ServiceConfigResponse"))
 * @SWG\Property(format="string", property="status", example="ok", description="Status return, ok or error")
 * @SWG\Property(type="string", property="config", example="{json string}", description="Summary of consumed configuration")
 * @SWG\Property(type="string", property="message", example="config failure", description="Any error message")
 **/
class ServiceConfigResponse {}
/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"name","url"}, type="object", @SWG\Xml(name="ConnectionConfig"))
 * @SWG\Property(format="string", property="name", example="Bonusing Engine", description="Name of the service")
 * @SWG\Property(format="string", property="url", example="https://bonusing.pawtest.gamelogic.com", description="URL of the service.")
 * @SWG\Property(format="string", property="client_id", example=3, description="Oauth client ID.")
 * @SWG\Property(format="string", property="client_secret", example="34fcssec3w", description="Oauth client secret.")
 * @SWG\Property(format="string", property="client_callback", example="http://my.client/callback", description="Oauth client redirect url.")
 **/
class ConnectionConfig {}