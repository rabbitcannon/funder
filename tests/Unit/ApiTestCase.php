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

    /**
     * Note that tests derived from ApiTestCase will use .env.testing by default.
     * This allows the use of a "sandbox" database separate from the working database;
     * but it must be created prior to testing.
     */
    public static function setUpBeforeClass()
    {
      parent::setUpBeforeClass();

      exec('php artisan migrate:reset --env=testing');
      exec('php artisan migrate --env=testing');
    }

    /**
     * This must be called at the top of EACH unit test that interacts with an EOS API
     * call; it disables only the auth middleware (ApiKey/OAuth2) but leaves in place
     * the ability to insert SPATs, correlation_id, model binding and so on.
     */
    public function disableAuthMiddleware()
    {
        $this->withoutMiddleware([
            \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
            \App\Http\Middleware\ApiKeyCheckMiddleware::class
        ]);
    }

    public function withoutMiddleware($middleware = [])
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
