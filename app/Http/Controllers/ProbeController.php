<?php
/**
 * Created by PhpStorm.
 * User: l_morris
 * Date: 12/13/2016
 * Time: 5:33 PM
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Endpoints;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Exception;

// this controller is common to all EOS services, and allows us to report general health
// back to EOS-MC. A 'ping' via /api/version just returns the current software version (tag).
// A 'probe' via /api/probe causes this service to ping its known peer services and return
// the results of those pings as an array.
class ProbeController extends Controller
{

    /**
     * @SWG\Get(
     *   path="/probe",
     *   summary="REQUIRED API FOR SERVICES: Execute peer probe",
     *   operationId="probe",
     *   tags={"eos"},
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="array",
     *       @SWG\Items(ref="#/definitions/ProbeResults"))
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     **/
    /**
     * Probe all peer services using /api/probe and appropriate authentication
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function probe(Request $request)
    {
        // check the peer url's that we know about and see how they respond to
        // an /api/version call
        $http = new Client([
            'headers' => [ 'Content-Type' => 'application/json',
                'Accept' => 'text/html']
        ]);
        $response = [];
        $time = null;
        // grab all the endpoint services we know about
        $endpoints = Endpoints::GetEndpoints();
        foreach($endpoints as $service) {
            $path = $service['url'] . '/api/version';
            try {
                $http->get($path, [
                    'on_stats' => function (TransferStats $stats) use (&$time) {
                        $time = $stats->getTransferTime();
                    }
                ]);
                $service['elapsed_time'] = $time;
                $service['status'] = 'ok';
            } catch (\Exception $e) {
                $service['elapsed_time'] = $time;
                $service['status'] = 'failure';
                $service['message'] = $e->getMessage();
            }
            $response[] = $service;
        }

        return response()->json($response);

    }

    /**
     * @SWG\Get(
     *   path="/version",
     *   summary="REQUIRED API FOR SERVICES: Respond with version number",
     *   operationId="version",
     *   tags={"eos"},
     * @SWG\Response(response=200, description="successful",
     *     @SWG\Schema(
     *       type="string",example="v1.0.1")
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     **/
    public function version(Request $request)
    {
        // 'git describe' only works if we have a tag; for DEV releases, we can add a little
        // file at the top level called version.php (gitignore it) that contains some string
        // like 'dev version'
        if (file_exists('version.php')) {
            return file_get_contents('version.php');
        }
        exec('git describe', $output);
        if(isset($output) && isset($output[0])) {
            return $output[0];
        }
        return "unknown";
    }
}

/**
 * strictly for Swagger doc
 * @SWG\Definition(required={"name","status","message"}, type="object", @SWG\Xml(name="ProbeResults"))
 * @SWG\Property(type="string", property="name", example="LPS Issuer", description="Name of peer service")
 * @SWG\Property(type="float", property="elapsed_time", example=0.94, description="Probe roundtrip time in seconds")
 * @SWG\Property(type="string", property="status", example="failure", description="Probe result status, ok or some error")
 * @SWG\Property(type="string", property="message", example="Not Authorized", description="Probe result message")
 **/
class ProbeResults {}