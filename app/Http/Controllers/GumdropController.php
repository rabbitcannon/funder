<?php

namespace App\Http\Controllers;

use Eos\Common\Exceptions\EosException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Gumdrop;
use Eos\Common\AuthPlayer;
use App\Player;
use App\EosWalletService;

//
// This entire controller, along with the Gumdrop and probably User model,
// as well as corresponding migrations, should be removed in your service.
// It is provided as an example only.
//
class GumdropController extends Controller
{
    public function __construct()
    {
        // all calls to /api/gumdrops require OAUTH2 client credentials token
        // you may place in your .env: L5_SWAGGER_API_AUTH_TOKEN="Bearer <my-bearer-token>"
        // for the simplest way to authorize the API
        // use the /oauth/token call to get the bearer token, see EOS API group
        // all EOS services *must* use the 'eos' middleware group to provide
        // transaction tracing and player/agent identification.
        // for security, the 'client' or 'scopes/scope' middleware (oauth2) is strongly
        // recommended but simple API key 'auth.key' may be used in some cases
        $this->middleware(['client','eos']);
    }

    /**
     * @SWG\Get(
     *   path="/api/gumdrops",
     *   summary="Get all the gumdrops",
     *   operationId="getAllGumdrops",
     *   tags={"gumdrops"},
     *   security={{"oauth2": {""}}},
     * @SWG\Parameter(
     *     in="query",
     *     name="correlation_id",
     *     description="GUID",
     *     type="string",
     *     required=false,
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/Gumdrop"))
     *   ),
     *   @SWG\Response(response=500, description="System error")
     *  )
     **/
    public function index()
    {
        // index returns all the whole gumdrops in the world
        return response()->json(Gumdrop::with('players')->get());
    }

    /**
     * @SWG\Post(
     *   path="/api/gumdrops",
     *   summary="Add a new gumdrop for a player",
     *   operationId="createGumdrop",
     *   tags={"gumdrops"},
     *   security={{"oauth2": {""}}},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Gumdrop Parameters.",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Gumdrop")
     *   ),
     * @SWG\Parameter(
     *     in="query",
     *     name="correlation_id",
     *     description="GUID",
     *     type="string",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     name="X-Auth-Spat",
     *     in="header",
     *     description="SciPlay Auth Header.",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful"),
     *   @SWG\Response(response=400, description="Validation error"),
     *   @SWG\Response(response=404, description="User not found"),
     *   @SWG\Response(response=500, description="System error")
     *  )
     *
     * @param Request $request
     * @throws Exception
     * @throws \Illuminate\Auth\AuthenticationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // do some validation
        $this->validate( $request,
            ['name' => 'required', 'color' => 'required']);

        // the standard way to fetch the player from SPAT
        // this returns an App\Player or throws
        $player = AuthPlayer::fetchOrFail()->player;

        // store me a new gumdrop for some player.

        $name = $request->get( 'name' );
        $color = $request->get( 'color' );
        // now I need to create the new Gumdrop, save it, and link it to my player.
        // I could do this here, but then my API route is the only way to create
        // gumdrops -- it's better to use a static model method.
        // Note that this call may throw and return an exception.
        $gumdrop = Gumdrop::createNewGumdropForPlayer([
            'name' => $name,
            'color' => $color], $player);

        // we are inserting here a test for a call to another EOS service
        // skip this if we're doing unit testing
        if( !app()->runningUnitTests() )
        {
            try
            {
                // this would normally be a class defined in Eos\Common.
                // a very simple service wrapper is included here as an example.
                $svc = new EosWalletService();
                $response = $svc->fetchAccounts();
                Log::info( "Test Wallet Response: " . json_encode( $response ) );
            }
            catch (EosException $e)
            { /* no op - if no WalletService configured, just continue */ }
        }

        return response()->json(['gumdrop' => $gumdrop], 200);
    }

    /**
     *  Player and/or Agent are identified by the X-Auth-Spat header.
     *
     * @SWG\Get(
     *   path="/api/players/gumdrops",
     *   summary="Get gumdrops for a player",
     *   operationId="getPlayerGumdrops",
     *   tags={"players"},
     *   security={{"oauth2": {""}}},
     * @SWG\Parameter(
     *     in="query",
     *     name="correlation_id",
     *     description="GUID",
     *     type="string",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     name="X-Auth-Spat",
     *     in="header",
     *     description="SciPlay Authentication Header.",
     *     required=true,
     *     type="string"
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/Gumdrop"))
     *   ),
     * @SWG\Response(response=404, description="Player not found"),
     * @SWG\Response(response=401, description="Unauthorized player"),
     * @SWG\Response(response=500, description="System error")
     *  )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function gumdropsForPlayer( Request $request )
    {
        // Get the authenticated player.
        $auth_player = AuthPlayer::fetchOrFail();

        // now go find all his gumdrops
        $my_gumdrops = $auth_player->player->gumdrops()->get();
        return response()->json($my_gumdrops);
    }

    /**
     * @SWG\Put(
     *   path="/api/gumdrops/{id}",
     *   summary="Modify a gumdrop",
     *   operationId="modifyGumdrop",
     *   tags={"gumdrops"},
     *   security={{"oauth2": {""}}},
     * @SWG\Parameter(
     *   in="path",
     *   name="id",
     *   description="Gumdrop Id",
     *   required=true,
     *   type="integer",
     *   ),
     * @SWG\Parameter(
     *     in="query",
     *     name="correlation_id",
     *     description="GUID",
     *     type="string",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     name="X-Auth-Spat",
     *     in="header",
     *     description="SciPlay Auth Header.",
     *     required=false,
     *     type="string"
     *   ),
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Gumdrop Parameters.",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Gumdrop")
     *   ),
     *   @SWG\Response(response=200, description="successful"),
     *   @SWG\Response(response=400, description="Validation error"),
     *   @SWG\Response(response=404, description="Gumdrop not found"),
     *   @SWG\Response(response=401, description="Unauthorized player"),
     *   @SWG\Response(response=500, description="System error")
     *  )
     *
     * @param Request $request
     * @param $gumdrop
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function update(Request $request, Gumdrop $gumdrop)
    {
        // do some validation
        $this->validate( $request,
            ['name' => 'required', 'color' => 'required']);

        $auth_player = AuthPlayer::fetchOrFail();

        // now are we allowed to alter this? either it needs to belong
        // to me, or it needs to have agent auth.
        $owner = $gumdrop->players()->first();
        $authorized = false;
        if( $owner && $auth_player->valid() && ($auth_player->registrar_id == $owner->registrar_id))
        { $authorized = true; }

        //todo: add $auth_agent check - set authorized if agent was given in SPAT
        if( ! $authorized )
        { return response()->json(['status' => 'Unauthorized player'],401); }


        $name = $request->get('name');
        $color = $request->get('color');
        $gumdrop->update( ['name' => $name, 'color' => $color ] );
        return response()->json( $gumdrop );
    }

    /**
     * @SWG\Delete(
     *   path="/api/gumdrops/{id}",
     *   summary="Delete a gumdrop",
     *   operationId="deleteGumdrop",
     *   tags={"gumdrops"},
     *   security={{"oauth2": {""}}},
     * @SWG\Parameter(
     *   in="path",
     *   name="id",
     *   description="Gumdrop Id",
     *   required=true,
     *   type="integer",
     *   ),
     * @SWG\Parameter(
     *     in="query",
     *     name="correlation_id",
     *     description="GUID",
     *     type="string",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     name="X-Auth-Spat",
     *     in="header",
     *     description="SciPlay Auth Header.",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful"),
     *   @SWG\Response(response=404, description="Gumdrop not found"),
     *   @SWG\Response(response=401, description="Unauthorized player"),
     *   @SWG\Response(response=500, description="System error")
     *  )
     *
     * @param $gumdrop
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function destroy(Gumdrop $gumdrop)
    {
        $auth_player = AuthPlayer::fetchOrFail();

        // now do we allow this? Either it needs to be my gumdrop, or
        // we need agent auth
        $owner = $gumdrop->players()->first();
        $authorized = false;
        if( $owner && $auth_player->valid() && ($auth_player->registrar_id == $owner->registrar_id))
        { $authorized = true; }
        //todo: add $auth_agent check - set authorized if agent was given in SPAT
        if( ! $authorized )
        { return response()->json(['status' => 'Unauthorized player'],401); }

        // this may throw - it's better just to let it, and have the Handler return
        // the 500 response.
        $gumdrop->delete();

        return response()->json([]);
    }
}
