<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Exception;

// this is an example of a persistent Player placeholder model.
// not all services will need to store Players, but since we attach
// Gumdrops to Players, we need a placeholder for the association.
class Player extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'registrar_id', 'first_name', 'last_name', 'email' ];
    
    protected $dates = [ 'created_at', 'updated_at' ];

    // don't return these in API calls
    protected $hidden = ['pivot','created_at','updated_at','id','first_name','last_name','email'];

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

