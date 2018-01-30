<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\SettingsSchema;
use Predis\ClientException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// this controller, or some variant of it, is included in any EOS service to allow push
// of endpoint configuration to the service. We can configure peer service endpoints
// either in our .env file or by receiving an /api/configure post
// from EOS-MC. The latter is the preferred method.
class ClientController extends Controller
{

    /**
     * @SWG\Get(
     *   path="/api/schema",
     *   summary="REQUIRED API FOR SERVICES: Return settings pack schema",
     *   operationId="settingsSchema",
     *   tags={"eos"},
     * @SWG\Parameter(
     *     name="apikey",
     *     in="query",
     *     type="string",
     *     description="required api key"
     *  ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function settingsSchema(Request $request)
    {
        $settings_schema = new SettingsSchema();
        $schema = $settings_schema->schema;
        // here is one place you could merge schemas, e.g.
        // $component = ["Gumdrops" => ["type"=>"group","fields"=> [
        //    "gumdropSize" => ["type"=>"enum","valid"=>["small","medium","large"],"value"=>"medium"],
        //    "gumdropColor" => ["type"=>"text","value"=>"red"]
        // ]]];
        // $settings_schema->mergeSchema($component);

        return response()->json($schema);
    }

    /**
     * @SWG\Get(
     *   path="/api/settings",
     *   summary="REQUIRED API FOR SERVICES: Return settings",
     *   operationId="getSettings",
     *   tags={"eos"},
     * @SWG\Parameter(
     *     name="apikey",
     *     in="query",
     *     type="string",
     *     description="required api key"
     *  ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string")
     *   ),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function getSettings(Request $request)
    {
        $settings = SettingsSchema::getRawSettings();
        return response()->json( $settings );
    }

    /**
     * @SWG\Post(
     *   path="/api/settings",
     *   summary="REQUIRED API FOR SERVICES: Accept settings",
     *   operationId="postSettings",
     *   tags={"eos"},
     * @SWG\Parameter(
     *     name="apikey",
     *     in="query",
     *     type="string",
     *     description="required api key"
     *  ),
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
        // decode the new settings
        $error = null;
        SettingsSchema::putRawSettings( $request->getContent(), $error );

        if($error)
        { return response()->json( ['Status' => 'Error', 'message' => 'Bad JSON detected, not updated'], 500 ); }

        return response()->json( ['Status' => 'Ok'] );
    }

    /**
     * @SWG\Delete(
     *   path="/api/settings",
     *   summary="REQUIRED API FOR SERVICES: Delete settings",
     *   operationId="deleteSettings",
     *   tags={"eos"},
     * @SWG\Parameter(
     *     name="apikey",
     *     in="query",
     *     type="string",
     *     description="required api key"
     *  ),
     * @SWG\Response(response=200, description="successful"),
     * @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function deleteSettings(Request $request)
    {
        SettingsSchema::clearRawSettings();
        return response()->json( ['Status' => 'Ok', 'message' =>"Settings deleted."] );
    }

    /**
     * @SWG\Get(
     *     path="/api/oauth/clients",
     *     summary="GET OAUTH CLIENTS: only used by eos-mc.",
     *     operationId="getOauthClients",
     *     tags={"eos"},
     * @SWG\Parameter(
     *     name="apikey",
     *     in="query",
     *     type="string",
     *     description="required api key"
     *  ),
     *  @SWG\Response(response=200, description="successful",
     *      @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/OauthClientResponse"))
     *   ),
     *   @SWG\Response(response=500, description="System error")
     * )
     */
    public function getOauthClients(Request $request)
    {
        // note that this method ASSUMES that Laravel Passport has been installed
        $clients = DB::table('oauth_clients')->get();
        $result = [];
        foreach( $clients as $client )
        {
            if( ! $client->revoked )
            {
                $result[] = [
                    'id' => $client->id,
                    'name' => $client->name,
                    'secret' => $client->secret,
                    'redirect' => $client->redirect
                ];
            }
        }
        return response()->json($result, 200);
    }

    /**
     * @SWG\Post(
     *     path="/api/oauth/clients",
     *     summary="CREATE OAUTH CLIENT: only used by eos-mc.",
     *     operationId="createOauthClient",
     *     tags={"eos"},
     * @SWG\Parameter(
     *     name="apikey",
     *     in="query",
     *     type="string",
     *     description="required api key"
     *  ),
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Client Data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/OauthClientData")
     *   ),
     *   @SWG\Response(response=200, description="successful",
     *      @SWG\Schema(ref="#/definitions/OauthClientResponse")),
     *   @SWG\Response(response=500, description="System error")
     * )
     */
    public function createOauthClient(Request $request)
    {
        $validator = Validator::make( $request->all(), [
            'name' => 'required|max:255',
            'redirect' => 'required|url']);
        if( $validator->fails() )
        {
            return response()->json( ['error' => 'VALIDATION',
                'message' => $validator->errors()->messages()] , 400 );
        }
        $client_data = $request->all();
        $clients = DB::table('oauth_clients')->get();
        $exists = false;
        foreach( $clients as &$client )
        {
            if( $client->redirect == $client_data['redirect'] )
            {
                $exists = true;
                $client_data['name'] = $client->name;
                $client_data['id'] = $client->id;
                $client_data['secret'] = $client->secret;
            }
        }
        if( ! $exists )
        {
            $client_data['secret'] = str_random( 40 );

            $client_data['id'] = DB::table( 'oauth_clients' )->insertGetId( [
                'user_id' => null,
                'name' => $client_data['name'],
                'secret' => $client_data['secret'],
                'redirect' => $client_data['redirect'],
                'personal_access_client' => false,
                'password_client' => false,
                'revoked' => false ] );
            // any exception goes to handler
        }
        return response()->json($client_data);
    }
}

/**
 * for convenience, we'll add the oauth/token api call in the 'eos' group as well, even though
 * it's actually provided by laravel/passport.
 */
/**
 * @SWG\Post(
 *   path="/oauth/token",
 *   summary="GET AN ACCESS TOKEN: You must know the client credentials id and secret.",
 *   operationId="getClientCredentialsToken",
 *   tags={"eos"},
 * @SWG\Parameter(
 *     name="body",
 *     in="body",
 *     description="Client Authorization",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/OauthGrantRequest")
 *   ),
 *   @SWG\Response(response=200, description="successful",
 *     @SWG\Schema(ref="#/definitions/OauthGrantResponse")),
 *   @SWG\Response(response=500, description="System error")
 *  )
 **/
class OauthPassport {}


/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"grant_type","client_id","client_secret","scope"}, type="object", @SWG\Xml(name="OauthGrantRequest"))
 * @SWG\Property(format="string", property="grant_type", example="client_credentials", description="Grant type")
 * @SWG\Property(type="string", property="client_id", example="2", description="ID of the credentials client")
 * @SWG\Property(type="string", property="client_secret", example="random-string", description="credentials client secret")
 * @SWG\Property(type="string", property="scope", example="manage-keys view-credentials", description="requested scopes")
 **/
class OauthGrantRequest {}
/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"access_token","refresh_token","expires_in"}, type="object", @SWG\Xml(name="OauthGrantResponse"))
 * @SWG\Property(format="string", property="access_token", example="long-string", description="use as Bearer token")
 * @SWG\Property(type="string", property="refresh_token", example="long-string", description="use to refresh expired token")
 * @SWG\Property(type="number", property="expires_in", example="445632", description="seconds until expiration")
 **/
class OauthGrantResponse {}
/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"id","name","secret","redirect",}, type="object", @SWG\Xml(name="OauthClientResponse"))
 * @SWG\Property(format="number", property="id", example="3", description="client Id")
 * @SWG\Property(format="string", property="name", example="eos-wallet", description="name of client")
 * @SWG\Property(type="string", property="secret", example="long-string", description="client Secret")
 * @SWG\Property(type="string", property="redirect", example="http:url", description="client redirect url")
 **/
class OauthClientResponse {}
/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"name","redirect",}, type="object", @SWG\Xml(name="OauthClientData"))
 * @SWG\Property(format="string", property="name", example="eos-wallet", description="name of client")
 * @SWG\Property(type="string", property="redirect", example="http:url", description="client redirect url")
 **/
class OauthClientData {}

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