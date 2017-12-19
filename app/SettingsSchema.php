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
        "Sample" => ["type" =>"group","fields"=>[
            "SweepTriggerLimit" => ["type"=>"number","value"=>"4500","valid"=>["min"=>1,"max"=>1000000]],
            "SweepPeriodSeconds" => ["type"=>"number","value"=>86400,"valid"=>["min"=>1000,"max"=>1000000]],
            "CheckRequestQueryPeriodSeconds"=>["type"=>"number","value"=>86400,"valid"=>["min"=>1000,"max"=>1000000]],
            "StandardLedgers" => ["type"=>"multigroup","extensible"=>true,"fields"=> [
                "name" => ["type"=>"text","sample"=>"ledger name"],
                "currency" => ["type"=>"enum","valid"=>["money","points"]],
                 ]
            ],
            "EventTypes" => ["type"=>"multigroup","extensible"=>true,"fields"=> [
                "name" => ["type"=>"text","valid"=>["sample"=>"TicketWin"]],
                "description" => ["type"=>"text","valid"=>["sample"=>"description of event"]],
                "attributes" => ["type"=>"multigroup","extensible"=>true, "fields"=> [
                    "name"=>["type"=>"text","sample"=>"amount"],
                    "type"=>["type"=>"enum","valid"=>["string","integer","boolean"]],
                    "jpath"=>["type"=>"text","sample"=>"/path/to/attribute"]]
                ]
            ]]
        ]
    ]];

    // if your service brings in a component, e.g. from Composer, which wants to extend the
    // configuration schema, it must merge its schema's top-level tag (group) with the master
    // schema (SettingsSchema::$schema), perhaps in a ServiceProvider::boot(). The top level
    // tags must be unique.
    //
    public static function mergeSchema($component_schema)
    {
        self::$schema = array_merge(self::$schema, $component_schema);
    }

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