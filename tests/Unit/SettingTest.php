<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Exception;

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
    public function test_we_can_validate_schema()
    {
      print( 'test_we_can_validate_schema' . PHP_EOL );
      try 
      {
          SettingsSchema::validate();
      }
      catch (Exception $e)
      {
          $this->assertTrue(false, "Unexpected exception {$e->getMessage()}");
      }
    }
    
    public function test_we_can_fail_schema_scalar()
    {
      print( 'test_we_can_fail_schema_scalar' . PHP_EOL );
      try
      {
        SettingsSchema::validate( ['scalar_element' => ['type' => 'number', 'bogus' => ''] ]);
        $this->fail("Expected exception not thrown");
      }
      catch (Exception $e)
      {
        print( 'Expected Exception: ' . $e->getMessage() . PHP_EOL );
      }
    }
    
    public function test_we_can_fail_schema_groups()
    {
      print( 'test_we_can_fail_schema_groups' . PHP_EOL );
      try
      {
        SettingsSchema::validate( [
             'schema' => ['type' => 'group',
                 'fields' => [
                     'services' => ['type' => 'multigroup', 'extensible' => true,
                             'fields' => [
                                 'name' => ['type' => 'text', 'sample' => 'Micro Service'],
                                 'class' => ['type' => 'text', 'sample' => 'App\MyClass'],
                                 'connections' => ['type' => 'group',
                                       'fields' => [
                                           'outbound' => ['type' => 'group',
                                               'fields' => [
                                                   "url" => ["type"=>"text","sample"=>"http://path.to.service"],
                                                   "authentication" => ["type"=>"bogus","valid"=>["oauth","apikey","none"]],
                                                   "clientid"=>["type"=>"text","sample"=>"20"],
                                               ],
                                           ],
                                       ], 
                                 ], 
                             ], 
                     ],
                 ]
             ] ] );
        $this->fail("Expected exception not thrown");
      }
      catch (Exception $e)
      {
        print( 'Expected Exception: ' . $e->getMessage() . PHP_EOL);
      }
    }
    
    public function test_we_can_get_schema()
    {
      print( 'test_we_can_get_schema' . PHP_EOL );
      $schema = SettingsSchema::get();
      $this->assertTrue(true);
    }
    
    public function test_we_can_get_default_values()
    {
      print( 'test_we_can_get_default_values' . PHP_EOL );
      $settings = SettingsSchema::getSchemaDefaults();
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
    
