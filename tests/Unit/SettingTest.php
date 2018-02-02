<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

use App\SettingsSchema;
use App\SettingPack;
use App\Setting;

class SettingTest extends ApiTestCase
{
    /**
     * An exhaustive class unit test.
     *
     * @return void
     */
    public function test_we_can_get_schema()
    {
      print( 'test_we_can_get_schema' . PHP_EOL );
      $schema = SettingsSchema::get();
      print( json_encode($schema) . PHP_EOL );
      $this->assertTrue(true);
    }
    
    public function test_we_can_get_default_values()
    {
      print( 'test_we_can_get_default_values' . PHP_EOL );
      $settings = SettingsSchema::getSchemaDefaults();
      print( json_encode($settings) . PHP_EOL );
      $this->assertTrue( TRUE );
    }
    
    public function test_we_can_get_effective_packs()
    {
      print( 'test_we_can_get_effective_packs' . PHP_EOL );
      $packs = SettingPack::inEffectiveOrder()->get();
      $this->assertEmpty( $packs );
    }
    
    public function test_we_can_set_current_pack()
    {
      print( 'test_we_can_set_current_pack' . PHP_EOL );
      $start = (new Carbon())->subMinute(10);
      $end = (new Carbon())->addHour();  //give us time to use it before it expires
      SettingPack::create(['quantum_start' => $start->timestamp,
                           'quantum_end' => $end->timestamp,
                           'pack' => ["gumdrop" => [ "color" => "blue"]] ]);
      $packs = SettingPack::inEffectiveOrder()->get();
      $this->assertNotEmpty( $packs );
    }
    
    public function test_we_can_get_current_color()
    {
      print( 'test_we_can_get_current_color' . PHP_EOL );
      $color = Setting::get('gumdrop.color');
      $this->assertEquals('blue', $color);
      $this->assertTrue( cache()->has( config('eos.run') . '_eos_settings' ) );
    }
    
    public function test_we_can_get_gumdrop_settings()
    {
      print( 'test_we_can_get_current_color' . PHP_EOL );
      $color = Setting::get('gumdrop');
      $this->assertArrayHasKey('color', $color);
    }
    
    public function test_we_can_change_gumdrop_setting()
    {
      print( 'test_we_can_change_gumdrop_setting' . PHP_EOL );
      Setting::set('gumdrop.color', 'green');
      $color = Setting::get('gumdrop');
      $this->assertArrayHasKey('color', $color);
    }
}
    
