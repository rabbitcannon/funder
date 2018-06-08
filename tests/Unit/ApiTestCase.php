<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

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
    protected static $hasMigrated = false;

    /**
     * Note that tests derived from ApiTestCase will use .env.testing by default.
     * This allows the use of a "sandbox" database separate from the working database;
     * but it must be created prior to testing.
     */
    public static function setUpBeforeClass()
    {
      parent::setUpBeforeClass();
    }

    /**
     * We are choosing a policy of persisting the database throughout a testing class. We will migrate/seed only once,
     * at the beginning of the first test.
     */
    public function setUp()
    {
        parent::setUp();
        if(! self::$hasMigrated)
        {
            Artisan::call('migrate:reset');
            Artisan::call('migrate');
            Artisan::call('db:seed');
            self::$hasMigrated = true;
        }

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

}
