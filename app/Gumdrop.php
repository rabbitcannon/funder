<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Player;

// this class/file is for example only, and should be removed
/**
 * @SWG\Definition(required={"name","color"}, type="object", @SWG\Xml(name="Gumdrop"))
 * @SWG\Property(format="int64", property="id", example=21, description="The gumdrop identifier.")
 * @SWG\Property(format="string", property="name", example="Bobby", description="Unique name of the gumdrop.")
 * @SWG\Property(format="string", property="color", example="red", description="The gumdrop color.")
 **/
class Gumdrop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color'
    ];

    // don't return pivot in API calls
    protected $hidden = ['pivot'];

    /**
     * @param $values - ['name', 'color']
     * @param $player - the Player to attach to
     * @return bool - true if success.
     */
    static function createNewGumdropForPlayer( $values, Player $player )
    {
        try {
            $gumdrop = new Gumdrop( $values );
            $gumdrop->save();
            $player->gumdrops()->attach($gumdrop);
        } catch ( \Exception $e ) {
            // this isn't always a good idea; sometimes it may be better to
            // just allow exceptions to be caught by the controller, or to
            // re-throw a new one. This approach simplifies the controller.
            return false;
        }
        return true;
    }

    /**
     * the concept of several players sharing a gumdrop is questionable
     * but we are, in fact, modeling it as many to many. belongsToMany
     * is the correct indicator for a pivot table e.g. gumdrop-player
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function players()
    {
        return $this->belongsToMany('App\Player');
    }
}
