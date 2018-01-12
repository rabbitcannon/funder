<?php
// Settings Schema Model for this service
// Your service will receive its settings based on this schema via a POST to /api/settings
//
namespace App;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Endpoints;

class SettingsSchema
{
    public $schema;

    function __construct()
    {
        // Remove this comment and change this to your own service schema.
        // This schema is provided as an example only.
        $this->schema = config('eos_sample_settings');

        // include the required eos_connections schema
        $connections = config('eos_connections');
        $this->mergeSchema($connections);

        // include the required eos_diagnostics schema
        $diagnostics = config('eos_diagnostics');
        $this->mergeSchema($diagnostics);
    }

    /**
     * if your service brings in a component, e.g. from Composer, which wants to extend the
    // configuration schema, it must merge its schema's top-level tag (group) with the master
    // schema (SettingsSchema::$schema), perhaps in a ServiceProvider::boot(). The top level
    // tags must be unique.
     * @param $component_schema - generally from config('my_schema_php')
     */
    public function mergeSchema( $component_schema )
    {
        $this->schema = array_merge($this->schema, $component_schema);
    }

    /**
     * SettingsSchema::fetch('ns.games') should intelligently fetch an array of all
     * games in 'ns.games' along with any defined in 'global.games'.
     * Likewise a simple scalar such as 'ns.notify.playerid', if not present, should fall back
     * to the value of 'global.notify.playerid', or if neither is present, any defined default.
     *
     * Normally settings are retrieved from Redis using our known installation/service key.
     *
     * @param $tag
     * @return array|mixed|null
     */
    public static function fetch( $tag )
    {
        /*todo: much better implementation */

        $settings = self::getRawSettings();

        $selected = self::modelElementsPrefixedBy($settings, 'global.'.$tag);;
        if( count($selected) == 0 )
        { return null; }
        else if( !is_array($selected) ) // simple scalar leaf
        { return $selected; }
        else
        {
            $selected_array = self::array_explode_recursive($selected, 'global.'.$tag);
            // a selection glitch leaves this nested with array tag ""
            return $selected_array[""];
        }
    }

    /**
     * SettingsSchema::place('ns.games.powerball.display','Powerball') will
     * alter the value of this one scalar tag in active settings. It currently
     * will not alter groups. This is handy for caching values determined at
     * run-time
     *
     * @param $tag
     * @param $value
     */
    public static function place( $tag, $value )
    {
        $settings = self::getRawSettings();
        $settings['global.'.$tag] = $value;
        $error = null;
        self::putRawSettings(json_encode($settings,true),$error);
        if( $error )
        { Log::error("place:".$tag." ERROR SAVING"); }
    }

    /**
     * return the input array filtered to just elements where the tags are prefixed
     * by the indicated string, e.g. for prefix 'global.games.pb' any elements with tags
     * such as 'global.games.pb.name, global.games.pb.desc' would be included.
     *
     * @param $settings_model
     * @param $prefix
     * @return array
     */
    private static function modelElementsPrefixedBy( $settings_model, $prefix )
    {
        $result = [];
        foreach( $settings_model as $tag => $value )
        {
            if( substr($tag,0,strlen($prefix)) == $prefix )
            { $result[$tag] = $value; }
        }
        return $result;
    }


    /**
     * convert from flat settings array:
     *  global.a => foo
     *  global.b => 3
     *  global.notify.c => bar
     *  global.notify.d => d1
     *  global.games.pb.name => pball
     *  global.games.pb.desc => desc
     *  ns.notify.c => nsbar
     * to an equivalent nested array:
     *  [global => [a=>foo, b=>3,
     *    notify=>[c=>bar,d=>d1],
     *    games=>[pb=>[name=>pball,desc=>desc]],
     *   ns => [notify=>[c=>nsbar]]
     * if the $settings_model is the subarray we are working on, say
     *   [global.notify.c => bar,global.notify.d => d1]
     * and the $prefix is the level, e.g. global.notify.
     * that should return [c=>bar,d=>d1]
     *
     * @param $settings_model
     * @param $prefix
     * @return array|null
     */
    private static function array_explode_recursive( $settings_model, $prefix )
    {
        if(!$settings_model)
        { return []; }
        $settings_output = [];
        $matching_prefix = '';
        foreach( $settings_model as $tag => $value )
        {
            // $tag is, say, global.notify.c
            $new_tag = substr($tag,strlen($prefix)); // $new_tag would be c
            $tag_atoms = explode('.',$new_tag); // ['c'] or an array with count>1 if not leaf

            if(!isset($tag_atoms[0]))
            { print("unexpected null tag!"); return null;} // shouldn't happen

            $base_tag =  $prefix . $tag_atoms[0];
            $new_prefix = $base_tag . '.'; // new prefix is global.notify.c in case we need to recurse
            if( isset($tag_atoms[1]) )
            {
                // we identify that we have sub-tags, so recursion needed
                if( $new_prefix == $matching_prefix )
                { continue; } // we already handled this prefix, skip through it
                $matching_prefix = $new_prefix;
                // collect all the tags with this prefix and process recursively
                $subarray = self::modelElementsPrefixedBy( $settings_model,$new_prefix );
                $settings_output[$tag_atoms[0]] = self::array_explode_recursive($subarray, $new_prefix);
            }
            else
            {
                // this is a leaf tag. just set its value.
                $settings_output[$tag_atoms[0]] = $value;
            }
        }
        return( $settings_output );
    }

    /**
     * return all the settings as an array
     * @return array|mixed
     */
    public static function getRawSettings()
    {
        $settings = [];
        $app_name = config('app.app_name');
        $install_prefix = config( 'app.install_prefix');
        //todo: a nested DB cache should backup Redis
        $settingsJson = Redis::get( $install_prefix . ':' . $app_name . ':settings');
        if( $settingsJson )
        {
            try
            { $settings = json_decode($settingsJson, true); }
            catch( \Exception $e )
            {
                error_log('Invalid Settings: '. $settingsJson );
                $settings = [];
            }
        }
        return $settings;
    }

    /**
     * validate and save the presented settings pack
     * @param $settingsJson
     * @param $error
     */
    public static function putRawSettings( $settingsJson, &$error )
    {
        $new_settings = [];
        try
        { $new_settings = json_decode( $settingsJson, true ); }
        catch( \Exception $e )
        { $error = 'Bad JSON detected, settings not updated'; }

        $settingsJson = json_encode( $new_settings );
        $install_prefix = config( 'app.install_prefix');

        // we want the complete settings pack in a 'unique' Redis key - ideally we want both
        // the app name and an installation prefix
        // e.g. MRB:my-service:settings
        //todo: a nested DB cache should backup Redis
        $app_name = config('app.app_name');
        Redis::set( $install_prefix . ':' . $app_name . ':settings', $settingsJson );

        // the settings now include our Connections (endpoint) info.
        // we need to pick these out and inform the Endpoints service.

        $endpoints_settings = SettingsSchema::fetch('Connections', $new_settings);
        Log::info(json_encode($endpoints_settings,true));

        // Redis tags like: 'MRB:my-service:endpoint:other-service'
        // will contain json-encoded value of the endpoint parameters
        foreach( $endpoints_settings['outbound'] as $name => $endpoint )
        {
            Redis::set( $install_prefix . ':' . $app_name . ':endpoint:' . $name,
                json_encode($endpoint,true) );
        }
        return;
    }

    /*
     * delete the settings for this service
     */
    public static function clearRawSettings()
    {
        $install_prefix = config( 'app.install_prefix');
        $app_name = config('app.app_name');
        Redis::del( $install_prefix . ':' . $app_name . ':settings' );
        return;
    }
}