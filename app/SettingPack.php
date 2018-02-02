<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\TimeBounded;

/**
 * @SWG\Definition(required={"pack"}, type="object", @SWG\Xml(name="SettingPack"))
 */

class SettingPack extends Model
{
    use TimeBounded;
    
    /**
     * @SWG\Property(format="int64", property="id", example=329, description="The setting pack identifier.")
     * @SWG\Property(type="timestamp", property="quantum_start", example=1483645580, description="Timestamp for pack effective time.")
     * @SWG\Property(type="timestamp", property="quantum_end", example=1483645580, description="Timestamp for pack expiration time.")
     * @SWG\Property(format="string", property="pack", example="{}", description="Json encoded settings pack")
     **/
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['quantum_start', 'quantum_end', 'pack'];
    
    /**
     * The attributes that requiring casting on input/output
     *
     * @var array
     */
    protected $casts = ['quantum_start' => 'timestamp',
                        'quantum_end' => 'timestamp',
                        'pack' => 'array' ];
    protected $dates = ['quantum_start', 'quantum_end'];
    
    /**
     * Query for all non-expired packs and return them in effective order. Don't look at the effective because we want all
     * future ones as well. 
     * 
     * @param unknown $query
     */
    public function scopeInEffectiveOrder( $query )
    {
        $now = new Carbon();
        return $query->where('quantum_end', '>', $now )
                     ->orderBy('quantum_start', 'asc');
    }
    
    public function scopeCurrent( $query )
    {
        $now = new Carbon();
        return $query->where('quantum_end', '>', $now )
                     ->where('quantum_start', '<', $now)
                     ->orderBy('quantum_start', 'desc')
                     ->limit( 1 );
    }
    
    public function scopeSubsequent( $query )
    {
        $now = new Carbon();
        return $query->where('quantum_start', '>', $now)
                     ->orderBy('quantum_start', 'asc')
                     ->limit( 1 );
    }
    
    /**
     * The expiration of a SettingPack occurs at the earlier of two timestamps, 1) The quantum_end of $this or 
     * 2) the quantum_start of the next future SettingPack, if a future instance exists. Note that we don't 
     * set the expiration time of the current pack. This allows us to remove a future pack and we automatically
     * fall back to the next previous working pack.
     */
    public function getExpiration()
    {
        $future_pack = SettingPack::subsequent()->first();
        return $future_pack ? Carbon::createFromTimestamp( $future_pack->quantum_start ) :
                              Carbon::createFromTimestamp( $this->quantum_end );
    }
}
