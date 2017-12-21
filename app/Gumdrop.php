<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Player;

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
     * @param $values - ['name', 'color', 'registrar_id']
     * @return bool - true if success.
     */
    static function createNewGumdropForPlayer( $values )
    {
        try {
            $gumdrop = new Gumdrop(['name' => $values['name'], 'color' => $values['color']]);
            $gumdrop->save();
            $player = Player::fetchPlayer($values['registrar_id']);
            $player->gumdrops()->attach($gumdrop);
        } catch ( \Exception $e ) {
            return false;
        }
        return true;
    }

    // the concept of several players sharing a gumdrop is questionable
    // but we are, in fact, modeling it as many to many
    public function players()
    {
        return $this->belongsToMany('App\Player');
    }
}
