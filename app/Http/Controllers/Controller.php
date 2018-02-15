<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Change the title to your own service name!
 *
 * @SWG\Swagger(
 *   basePath="/",
 *   consumes={"application/json"},
 *   produces={"application/json"},
 *   @SWG\Info(
 *     title="Base Service API",
 *     version="1.0.0"
 *   ),

 *   @SWG\SecurityScheme(
 *      securityDefinition="oauth2", type="oauth2", description="OAuth2 Client Grant", flow="application",
 *      tokenUrl="/oauth/token",
 *      scopes={"scope": ""}
 *   )
 * )
 **/
class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}