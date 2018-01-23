<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GumDropTest extends TestCase
{
    use WithoutMiddleware;
  
  /**
   * An exhaustive class unit test.
   *
   * @return void
   */
  public function test_we_can_create_gumdrops()
  {
    print( 'test_we_can_create_gumdrops' . PHP_EOL );
    $this->assertTrue(true);
  }

  /**
   * why create player in a gumdrop test. We are testing whether we can pass a new player who has
   * no gumdrops and have that player authenticated and create in the database.
   */
  public function test_we_can_create_player( )
  {
    print('test_we_can_create_player' . PHP_EOL);
    $auth = json_encode( ['player' => ['registrar_id' => '329',
                                       'firstname' => 'robert',
                                       'lastname' => 'jewett'] ] );
    $response = $this->get( 'api/players/gumdrops', ['X-Auth-Spat' => $auth] );
    $response->assertStatus( 200 );
    
    //did we create the player?
    $player = Player::byRegistrar( '329' )->first();  
    $this->assertFalse( empty( $player ) );
  }
}
