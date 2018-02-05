<?php
namespace Tests\Unit;

use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    protected static $client;

    public static function setUpBeforeClass()
    {
      parent::setUpBeforeClass();

      exec('php artisan migrate:reset --env=testing');
      exec('php artisan migrate --env=testing');
    }
}
