<?php
// Settings Schema Model for this service
// Your service will receive its settings based on this schema via a POST to /api/settings
//
namespace App;

use Illuminate\Support\Facades\Redis;

class SettingsSchema
{
    // Remove this comment and change this to your own service schema.
    // This schema is provided as an example only.
    public static $schema = [
        "color" => ["type"=>"text","value"=>"red"],
        "count" => ["type"=>"number","value"=>1,"valid"=>["min"=>1,"max"=>10]],
        "notify" => ["type"=>"group","fields"=> [
            "playerid" => ["type"=>"text"],
            "by" => ["type"=>"enum","valid"=>["email","push","messagecenter"],"value"=>"email"]
            ]
        ],
        "games" => ["type"=>"multigroup","extensible"=>true,"fields"=> [
            "name" => ["type"=>"text","valid"=>["sample"=>"Powerball"]],
            "desc" => ["type"=>"text","valid"=>["sample"=>"A multi-state draw game"]],
            "effective" => ["type"=>"text","valid"=>["regex"=>"\d{4}-\d{2}-\d{2}","sample"=>"YYYY-MM-DD"]]]
        ]
    ];

    // SettingsSchema::fetch('ns.games') should intelligently fetch an array of all
    // games in 'ns.games' along with any defined in 'global.games' - as well as checking
    // for any timestamp overrides such as '@1568473883.ns.games' or '@1568473883.global.games'.
    // Likewise a simple scalar such as 'ns.notify.playerid', if not present, should fall back
    // to the value of 'global.notify.playerid', or if neither is present, any defined default.
    //
    public static function fetch($tag)
    {
        /*todo: implementation */
    }

    public static function getRawSettings()
    {
        $settings = [];
        $settingsJson = Redis::get( 'settings' );
        if( $settingsJson ) {
            try {
                $settings = json_decode($settingsJson, true);
            } catch( \Exception $e ) {
                error_log('Invalid Settings: '. $settingsJson );
                $settings = [];
            }
        }
        return $settings;
    }

    public static function putRawSettings( $settingsJson, &$error )
    {
        $new_settings = [];
        try
        { $new_settings = json_decode( $settingsJson, true ); }
        catch( \Exception $e )
        { $error = 'Bad JSON detected, settings not updated'; }

        $settingsJson = json_encode( $new_settings );
        Redis::set( 'settings', $settingsJson );
        return;
    }

    public static function clearRawSettings()
    {
        Redis::del( 'settings' );
        return;
    }
}