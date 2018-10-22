<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Eos\Common\WalletService;
use App\Player;
use App\Exceptions\FundingException;
use Eos\Common\InteractiveCoreService;


//
class FundingController extends Controller
{

    /**
     * @SWG\Post(
     *   path="/api/funding/login",
     *   summary="Log in a player",
     *   operationId="logInPlayer",
     *   tags={"funding"},
     * @SWG\Parameter(
     *     in="body",
     *     name="credentials",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/LoginCredentials")
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/Player"))
     *   ),
     *   @SWG\Response(response=550, description="Failed login exception")
     *  )
     **/
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'registrar_id' => 'required'
        ]);

        $icore = new InteractiveCoreService();
        $auth = $icore->loginPlayer( $request->input('email'), $request->input('password') );

        if( $auth['code'] != 200)
        { throw new FundingException('_AUTHERROR',['message' => $auth['message']] ); }

        $playerdata = $icore->getPlayerInformation( $request->input('registrar_id') );

        if( !$playerdata || $playerdata->Email != $request->input('email') )
        { throw new FundingException( '_AUTHERROR',['message' => 'missing player info']); }

        $construct_player = [
            'registrar_id' => strval($playerdata->PlayerCardId),
            'activateddatetime' => $playerdata->ActivatedDateTime,
            'lastlogindatetime' => $playerdata->LastLoginDateTime,
            'cashbalancepence' => intval($playerdata->CashBalancePence),
            'username' => $playerdata->UserName,
            'firstname' => $playerdata->FirstName,
            'lastname' => $playerdata->LastName,
            'phone' => $playerdata->Phone,
            'email' => $playerdata->Email,
            'address1' => $playerdata->Address1,
            'address2' => $playerdata->Address2,
            'city' => $playerdata->City,
            'state' => $playerdata->State,
            'zip' => $playerdata->PostalCode,
            'playerstate' => $playerdata->PlayerState
        ];

        $match_player = new Player($construct_player);
        $hash = $match_player->makeHash();
        $player = Player::byHash($hash)->first();
        if( !$player )
        {
            $match_player->save();
            $player = $match_player;
        }

        $ws = new WalletService();
        $accounts = $ws->getAccounts($player);
        $funding = $ws->getFundingOptions($player);

        return response()->json([
            'player' => $player,
            'accounts' => $accounts,
            'funding' => $funding
        ]);
    }

    public function addPaymentMethod(Request $request) {
//        $type, $nickname, $details, $default, $player, $agent = null
        $info = json_decode($request->getContent(), true);

        $type = $info['funding_method_type'];
        $nickname = $info['payment_method_nickname'];
        $token = $info['provider_temporary_token'];
        $details = [

        ];


        $hash = $info['playerHash'];
        $player = Player::byHash($hash)->first();

        $ws = new WalletService();
        $ws->addPaymentMethod($type, $nickname, $details, $default, $player);
    }

    public function fundWallet(Request $request) {
        $info = json_decode($request->getContent(), true);

        $type = "token";
        $token = $info['provider_temporary_token'];
        $address = [
            'address_nickname' => $info['billing_details']['address_nickname'],
            'address1' => $info['billing_details']['address1'],
            'address2' => $info['billing_details']['address2'],
            'city' => $info['billing_details']['city'],
            'state' => $info['billing_details']['state'],
            'country' => $info['billing_details']['country'],
            'zip' => $info['billing_details']['zip'],
        ];
        $profile_id = null;
        $amount = $info['amount'];
        $hash = $info['playerHash'];
        $player = Player::byHash($hash)->first();

        $ws = new WalletService();
        $ws->fundWalletAccount($type, $token, $address, $profile_id, $amount, $player);

        return response()->json(
            $ws->fundWalletAccount($type, $token, $address, $profile_id, $amount, $player)
        );
    }

    /**
     * @SWG\Get(
     *   path="/api/funding",
     *   summary="Get stored funding methods",
     *   operationId="getFunding",
     *   tags={"funding"},
     * @SWG\Parameter(
     *     in="query",
     *     name="playerhash",
     *     required=true,
     *     type="string",
     *     description="required player hash from login"
     *   ),
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/FundingList"))
     *   ),
     *   @SWG\Response(response=550, description="Failed login exception")
     *  )
     **/
    public function getFunding( Request $request)
    {
        $request->validate([
            'playerhash' => 'required'
        ]);

        $player = Player::byHash($request->input('playerhash'))->first();
        if( !$player )
        { throw new AuthenticationException(); }

        $wallet = new WalletService();
        $funding = $wallet->getFundingOptions( $player, null );

        return response()->json($funding);
    }

    /**
     * Other funding methods here
     */
}

/**
 * @SWG\Definition(required={"email","password","registrar_id"}, type="object", @SWG\Xml(name="LoginCredentials"))
 * @SWG\Property(format="string", property="email", example="me@email.com", description="The player email credential.")
 * @SWG\Property(format="string", property="password", example="xxxyyy", description="The player password credential.")
 * @SWG\Property(format="string", property="registrar_id", example="224", description="The player registrar id (playercardid).")
 **/
class LoginCredentials {}

/**
 * @SWG\Definition(required={}, type="object", @SWG\Xml(name="FundingList"))
 * @SWG\Property(property="eft-profiles", description="All stored EFT profiles for the player.",
 *     type="array",
 *       @SWG\Items(ref="#/definitions/EftProfile"))
 * @SWG\Property(property="card-profiles", description="All stored Card profiles for the player.",
 *     type="array",
 *       @SWG\Items(ref="#/definitions/CardProfile"))
 **/
class FundingList {}

/**
 * @SWG\Definition(required={"provider","name"}, type="object", @SWG\Xml(name="EftProfile"))
 * @SWG\Property(format="int64", property="id", example=21, description="EFT Profile identifier.")
 * @SWG\Property(format="string", property="name", example="Moms Bank Account", description="Player selected name")
 * @SWG\Property(format="string", property="provider", example="PaySafe", description="The e-commerce provider in use")
 * @SWG\Property(format="string", property="provider_funding_token", example="GGX435R-21er42-d345", description="Provider external ID for profile")
 * @SWG\Property(format="string", property="card_type", example="MasterCard", description="Card type identifier")
 * @SWG\Property(format="string", property="state", example="active", description="State of the profile: active, expired, pending")
 * @SWG\Property(format="boolean", property="is_default_funding", example=true, description="True if this is default funding option")
 **/
class EftProfile {}

/**
 * @SWG\Definition(required={"provider","name"}, type="object", @SWG\Xml(name="CardProfile"))
 * @SWG\Property(format="int64", property="id", example=21, description="EFT Profile identifier.")
 * @SWG\Property(format="string", property="name", example="Moms Bank Account", description="Player selected name")
 * @SWG\Property(format="string", property="provider", example="PaySafe", description="The e-commerce provider in use")
 * @SWG\Property(format="string", property="provider_funding_token", example="GGX435R-21er42-d345", description="Provider external ID for profile")
 * @SWG\Property(format="string", property="card_type", example="MasterCard", description="Card type identifier")
 * @SWG\Property(format="string", property="state", example="active", description="State of the profile: active, expired, pending")
 * @SWG\Property(format="boolean", property="is_default_funding", example=true, description="True if this is default funding option")
 **/
class CardProfile {}
