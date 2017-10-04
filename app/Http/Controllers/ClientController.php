<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
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
     *   path="/settings",
     *   summary="REQUIRED API FOR SERVICES: Return settings pack names",
     *   operationId="settingsList",
     *   tags={"eos"},
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(type="string"))
     *   ),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function settingsList(Request $request)
    {
        $settings = [];
        $settingsJson = Redis::get( 'settings-packs' );
        if( $settingsJson ) {
            $settings = json_decode( $settingsJson, true );
        }
        return response()->json( $settings );
    }

    /**
     * @SWG\Get(
     *   path="/settings/{pack_id}",
     *   summary="REQUIRED API FOR SERVICES: Return specific settings pack",
     *   operationId="getSettingsPack",
     *   tags={"eos"},
     * @SWG\Parameter(
     *   in="path",
     *   name="pack_id",
     *   description="Unique Pack Identifier",
     *   required=true,
     *   type="string",
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string")
     *   ),
     *   @SWG\Response(response=404, description="settings pack not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function getSettingsPack(Request $request, $pack_id)
    {
        $settings = [];
        $settingsJson = Redis::get( 'settings-packs' );
        if( $settingsJson ) {
            $settings = json_decode( $settingsJson, true );
        }
        if( !in_array( $pack_id, $settings ) )
        { return response()->json( ['message' => 'Could not find pack '.$pack_id], 404 ); }
        return response()->json( Redis::get( 'PACK.'.$pack_id ) );
    }

    /**
     * @SWG\Post(
     *   path="/settings/{pack_id}",
     *   summary="REQUIRED API FOR SERVICES: Accept settings pack",
     *   operationId="postSettings",
     *   tags={"eos"},
     * @SWG\Parameter(
     *   in="path",
     *   name="pack_id",
     *   description="Unique Pack Identifier",
     *   required=true,
     *   type="string",
     *   ),
     * @SWG\Parameter(
     *   in="body",
     *   name="body",
     *   description="Pack Value Data",
     *   required=true,
     *   @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=200, description="successful"),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function postSettings(Request $request, $pack_id)
    {
        // get our current list of settings packs; may need to add a new pack id
        $settings = [];
        $settingsJson = Redis::get( 'settings-packs' );
        if( $settingsJson ) {
            $settings = json_decode( $settingsJson, true );
        }
        // add to the list if not already present
        if( !in_array( $pack_id, $settings ) ) {
            $message = "New pack ".$pack_id;
            $settings[] = $pack_id;
            $settingsJson = json_encode( $settings );
            Redis::set( 'settings-packs', $settingsJson );
        } else {
            $message = "Replaced pack ".$pack_id;
        }
        // decode to validate good JSON
        try
        { $pack = json_decode( $request->getContent(), true ); }
        catch( \Exception $e )
        { return response()->json( ['message' => 'Bad JSON detected, not updated'], 500 ); }

        // add or update the pack with body contents
        Redis::set( 'PACK.'.$pack_id, $request->getContent() );

        return response()->json( ['message' => $message] );
    }

    /**
     * @SWG\Delete(
     *   path="/settings/{pack_id}",
     *   summary="REQUIRED API FOR SERVICES: Delete settings pack",
     *   operationId="deleteSettings",
     *   tags={"eos"},
     * @SWG\Parameter(
     *   in="path",
     *   name="pack_id",
     *   description="Unique Pack Identifier",
     *   required=true,
     *   type="string",
     *   ),
     * @SWG\Response(response=200, description="successful"),
     * @SWG\Response(response=404, description="settings pack not found"),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function deleteSettings(Request $request, $pack_id)
    {
        $settings = [];
        $settingsJson = Redis::get( 'settings-packs' );
        if( $settingsJson ) {
            $settings = json_decode( $settingsJson, true );
        }
        if( !in_array( $pack_id, $settings ) )
        { return response()->json( ['message' => 'Could not find pack '.$pack_id], 404 ); }
        else
        {
            unset($settings[ array_search($pack_id,$settings) ]);
            $settingsJson = json_encode( $settings );
            Redis::set( 'settings-packs', $settingsJson );
        }

        Redis::del('PACK.'.$pack_id);
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