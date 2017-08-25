<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @SWG\Swagger(
 *   basePath="/api",
 *   consumes={"application/json"},
 *   produces={"application/json"},
 *   @SWG\Info(
 *     title="Boogers API",
 *     version="1.0.0"
 *   )
 * )
 **/
class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
