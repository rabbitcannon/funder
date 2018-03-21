<?php

namespace Tests\Unit;

use Tests\Unit\ApiTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GumDropTest extends ApiTestCase
{
  private static $gumdrop_id;
  /**
   * An exhaustive class unit test.
   * For API calls, call $this->disableAuthMiddleware() before making
   * the $this->json call
   *
   * @return void
   */
    public function test_we_can_create_a_gumdrop()
    {
    print( 'test_we_can_create_a_gumdrop' . PHP_EOL );

    $this->disableAuthMiddleware();

    $auth = json_encode( ['player' => ['registrar_id' => '1',
          'firstname' => 'mickey',
          'lastname' => 'mouse'] ] );

    $response = $this->json( 'post', '/api/gumdrops', [
          'color' => 'blue',
          'name' => 'a blue gumdrop'],
        ['X-Auth-Spat' => $auth]);

    $response->assertStatus(200);
    $gumdrop = $response->json();
    self::$gumdrop_id =$gumdrop['gumdrop']['id'];

    }

    public function test_we_can_modify_a_gumdrop()
    {
        print( 'test_we_can_modify_a_gumdrop' . PHP_EOL );

        $this->disableAuthMiddleware();

        $auth = json_encode( ['player' => ['registrar_id' => '1',
            'firstname' => 'mickey',
            'lastname' => 'mouse'] ] );

        $response = $this->json( 'put', '/api/gumdrops/'.self::$gumdrop_id, [
            'color' => 'purple',
            'name' => 'a purple gumdrop'],
            ['X-Auth-Spat' => $auth]);

        $response->assertStatus(200);

    }

    public function test_we_cannot_modify_someone_elses_gumdrop()
    {
        print( 'test_we_cannot_modify_someone_elses_gumdrop' . PHP_EOL );

        $this->disableAuthMiddleware();

        $auth = json_encode( ['player' => ['registrar_id' => '2',
            'firstname' => 'minnie',
            'lastname' => 'mouse'] ] );

        $response = $this->json( 'put', '/api/gumdrops/'.self::$gumdrop_id, [
            'color' => 'green',
            'name' => 'a green gumdrop'],
            ['X-Auth-Spat' => $auth]);

        $response->assertStatus(401);

    }


    public function test_we_can_delete_a_gumdrop()
    {
        print( 'test_we_can_delete_a_gumdrop' . PHP_EOL );

        $this->disableAuthMiddleware();

        $auth = json_encode( ['player' => ['registrar_id' => '1',
            'firstname' => 'mickey',
            'lastname' => 'mouse'] ] );

        $response = $this->json( 'delete', '/api/gumdrops/'.self::$gumdrop_id,[],
            ['X-Auth-Spat' => $auth]);

        $response->assertStatus(200);
    }
}
