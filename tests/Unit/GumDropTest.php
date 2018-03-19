<?php

namespace Tests\Unit;

use Tests\Unit\ApiTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GumDropTest extends ApiTestCase
{
  
  /**
   * An exhaustive class unit test.
   *
   * @return void
   */
  public function test_we_can_create_gumdrops()
  {
    print( 'test_we_can_create_gumdrops' . PHP_EOL );

    $this->withoutMiddleware([
        \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
        \App\Http\Middleware\ApiKeyCheckMiddleware::class
    ]);

    $auth = json_encode( ['player' => ['registrar_id' => '1',
          'firstname' => 'mickey',
          'lastname' => 'mouse'] ] );

    $response = $this->json( 'post', '/api/gumdrops', [
          'color' => 'blue',
          'name' => 'a blue gumdrop'],
        ['X-Auth-Spat' => $auth]);

    $response->assertStatus(200);

  }

}
