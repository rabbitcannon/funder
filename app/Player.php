<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Exception;

/**
 * @SWG\Definition(required={"playercardid"}, type="object", @SWG\Xml(name="Player"))
 * @SWG\Property(format="string", property="playerhash", example="X234ERtui*7$", description="Player hash value to include on future calls")
 * @SWG\Property(format="int64", property="registrar_id", example=21, description="The playercard identifier.")
 * @SWG\Property(format="int64", property="activateddatetime", example=1382128779, description="Timestamp of player activation.")
 * @SWG\Property(format="int64", property="lastlogindatetime", example=1382129829, description="Timestamp of most recent login.")
 * @SWG\Property(format="int64", property="cashbalancepence", example=440000, description="Available wagering cash in pence.")
 * @SWG\Property(format="string", property="username", example="fred", description="Player username (non-unique).")
 * @SWG\Property(format="string", property="firstname", example="Bobby", description="Player first name.")
 * @SWG\Property(format="string", property="lastname", example="Blagg", description="Player last name.")
 * @SWG\Property(format="string", property="phone", example="2223334444", description="The player phone number")
 * @SWG\Property(format="string", property="email", example="me@email.com", description="The player email.")
 * @SWG\Property(format="string", property="address1", example="224 some street", description="The player address.")
 * @SWG\Property(format="string", property="address2", example="apt 22", description="The player address continued.")
 * @SWG\Property(format="string", property="city", example="Columbus", description="The player city of residence.")
 * @SWG\Property(format="string", property="state", example="OH", description="The player state of residence")
 * @SWG\Property(format="string", property="zip", example="20421", description="The player postal code.")
 * @SWG\Property(format="int64", property="playerstate", example=96, description="Player state bitmask.")
 **/
class Player extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'registrar_id', 'playerhash', 'activateddatetime', 'lastlogindatetime', 'cashbalancepence', 'username', 'firstname', 'lastname',
        'phone', 'email', 'address1', 'address2', 'city', 'state', 'zip', 'playerstate' ];
    
    protected $dates = [ 'created_at', 'updated_at' ];

    // don't return these in API calls
    protected $hidden = ['created_at','updated_at','id'];

    /**
     * @param Builder $query
     * @param $trigger
     * @return mixed
     */
    public function scopeByRegistrar($query, $registrarid)
    {
        return $query->where('registrar_id', $registrarid);
    }

    /**
     * @param Builder $query
     * @param $trigger
     * @return mixed
     */
    public function scopeByHash($query, $playerhash)
    {
        return $query->where('playerhash', $playerhash);
    }

    /**
     * Construct a hash that should match player over time, unless basic contact changes. Must save player after this.
     *
     */
    public function makeHash()
    {
        $hashvalues = [$this->activateddatetime,$this->email,$this->registrar_id,$this->firstname,$this->lastname,$this->phone,$this->zip];
        $hash = hash( 'sha256', json_encode($hashvalues) );
        $this->playerhash = $hash;
        return $hash;
    }

    /**
     * Fetch a db Player or create a new one. Db exceptions can be thrown. There is a small but non-zero chance that a player
     * could enter two events simultaneously and cause a duplicate key error. We will retry on failure but just once so that
     * we don't spin in the event that the db is down.
     * 
     * @param $registrar_id
     * @return \App\Player
     */
    public static function fetchPlayer( $registrar_id )
    {
        $player = Player::byRegistrar( $registrar_id )->first();
        if( ! $player )
        {
            try
            {
              $player = new Player();
              $player->registrar_id = $registrar_id;
              $player->save();
            }
            catch( Exception $e )
            {
              $player = Player::byRegistrar( $registrar_id )->first();
            }
        }

        if( ! $player )  //must not have been a duplicate issue.
        { throw $e; }

        return $player;
    }

    /**
     * a player may have zero or more gumdrops via gumdrop-player pivot
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function gumdrops()
    {
        return $this->belongsToMany('App\Gumdrop');
    }
}

