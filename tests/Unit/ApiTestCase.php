<?php
namespace Tests\Unit;

use Tests\TestCase;

class NullMiddleware
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}

abstract class ApiTestCase extends TestCase
{
    protected static $client;

    public static function setUpBeforeClass()
    {
      parent::setUpBeforeClass();

      exec('php artisan migrate:reset --env=testing');
      exec('php artisan migrate --env=testing');
    }

    public function withoutMiddleware(array $middleware = [])
    {
        if (empty($middleware)) {
            return parent::withoutMiddleware();
        }

        foreach ($middleware as $abstract) {
            $this->app->instance($abstract, new NullMiddleware);
        }

        return $this;
    }
}
