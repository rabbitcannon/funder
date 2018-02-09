<?php

namespace App;

use Peekmo\JsonPath\JsonStore;
use App\SettingPack;

class Setting
{
    protected static $local_cache = NULL;  //optimize multiple calls in a thread
    
    /**
     * Get a RUN specific configuration setting. If the setting has been set at runtime it will be available in 
     * the cache or database (SettingPack). If not set at runtime it may be available as a default in the 
     * Schema. If not found in either location we return the default parameter. 
     * 
     * @param string $setting
     * @param mixed $default - pass [] to return an array of 1 element
     * @return mixed
     */
    public static function get( $setting, $default = NULL )
    {
        $jsonpath = new JsonStore( self::getJsonSettings() );
        $results = $jsonpath->get( "$.$setting" );
        
        //$results will be an empty array if none found, it will be an array of a single element if we matched
        //one element (usually a scalar). If the array contains 1 element you probably want the scalar value. 
        //In this case we would return the array element, but there are cases where you expect to get an array
        //of elements and it just happens that the array has only 1 member. If you pass the default as an array
        //we will return the result as an array of 1 member. 
        if( empty( $results ) )
        { return $default; }
        if( count($results) == 1 )
        { return is_array( $default ) ? $results : $results[0]; }
        return $results;
    }

    /**
     * Return the current SettingPack. There must always be a current pack.
     */
    public static function getCurrent()
    {
        $setting_pack = SettingPack::current()->firstOrFail();
        return $setting_pack->pack;  //this is the settings decoded by eloquent
    }
    
    /**
     * Update the value of a single key in the current Pack. We also clear the cache to force a reload of
     * the changed settings
     * 
     * @param string $setting
     * @param mixed $value
     */
    public static function set( $setting, $value )
    {
        $jsonpath = new JsonStore( self::getJsonSettings() );
        $jsonpath->set( "$.$setting", $value );
        
        self::clearCache();
        
        //save the updated settings to the current SettingPack. The next use of Setting will reload from db.
        //JsonStore is a protected format. Its toString will give us the json which we decode to pass to 
        //Eloquent
        $setting_pack = SettingPack::current()->firstOrFail();
        $setting_pack->pack = json_decode( $jsonpath->toString() );
        $setting_pack->save();
    }
    
    /**
     * Update the current SettingPack with a new setting json. We flush all cached values to force a reload.
     * Creating or updating a future SettingPack is done elsewhere as this requires knowledge of the 
     * effective timestamp of the future Pack. 
     * 
     * @param array $setting_data - raw (non-encoded) settings
     */
    public static function storeCurrent( $setting_data )
    {
        //clear the existing cache values.
        self::clearCache();
        
        //save the setting_data to the current SettingPack. The next use of Setting will reload from db.
        $setting_pack = SettingPack::current()->firstOrFail();
        $setting_pack->pack = $setting_data;
        $setting_pack->save();
    }
    
    /**
     * Fetch the current Settings JSON and optionally cache the result. If we are about to update Settings
     * there is no need to cache because we are about the flush the cache, so pass false. The default is to 
     * cache locally and in the Laravel cache. We cache locally so that in one thread we always use the 
     * settings as defined at the start and to optimize performance. 
     * 
     * @param string $tocache - should we cache the settings?
     * @return string|unknown - the json result
     */
    public static function getJsonSettings( $tocache = true )
    {
        if( self::$local_cache )
        { return self::$local_cache; }
        elseif( cache()->has( self::getCacheKey() ) )
        { 
            $json = self::$local_cache = cache()->get( self::getCacheKey(), [] ); 
            if( $tocache )
            { self::$local_cache = $json;}
        }
        else
        {
            //if not found in the static cache or the laravel cache, we instatiate from the SettingPack (db).
            $schema_defaults = SettingsSchema::getSchemaDefaults();
            $setting_pack = SettingPack::current()->firstOrFail();
            $settings = $setting_pack->pack;  
            $json = self::$local_cache = json_encode( array_replace_recursive( $schema_defaults, $settings ) );
            if( $tocache )
            {
                self::$local_cache = $json;
                cache()->put( self::getCacheKey(), self::$local_cache, $setting_pack->getExpiration() );
            }
        }
        return $json;
    }
    
    /**
     * Clear all existing cached values. The class is caching in the thread and globally we use laravel cache. 
     */
    public static function clearCache()
    {
        self::$local_cache = NULL;
        cache()->forget( self::getCacheKey() );
    }

    public static function getCacheKey()
    {
        return config('eos.run') . '_eos_settings';
    }
    
}

