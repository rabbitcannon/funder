<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Exception;
use App\Player;

// this class/file is for example only, and should be removed
/**
 * @mixin \Eloquent
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
     * Attempt to create a Gumdrop and attach to the indicated player.
     * @param $values - ['name', 'color']
     * @param $player - the Player to attach to
     * @return Gumdrop - if successful.
     * @throws Exception
     */
    static function createNewGumdropForPlayer( $values, Player $player )
    {
        $gumdrop = new Gumdrop( $values );
        $gumdrop->save();
        $player->gumdrops()->attach($gumdrop);

        return $gumdrop;
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
