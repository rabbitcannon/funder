<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\Endpoints;
use App\SettingsSchema;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Middleware;
use App\ApiTraceLogger;


class EOSService
{
    public static $transaction_id;
    public static $auth_header;
    public $service_name;
    public $service_url;
    private $auth_type;
    private $client_id;
    private $client_secret;
    private $guzzle_client;
    private $guzzle_options;
    private $slow_threshold;

    /**
     * EOSService constructor.
     * @param $name - must match one in config('app.known_services')
     */
    public function __construct( $name )
    {
        $this->service_name = $name;
        $endpoint = Endpoints::GetEndpoint( $this->service_name );

        if( $endpoint )
        {
            $this->service_url = $endpoint['url'];
            $this->auth_type = $endpoint['auth'];
            $this->client_id = isset($endpoint['client_id']) ? $endpoint['client_id'] : '';
            $this->client_secret = isset($endpoint['client_secret']) ? $endpoint['client_secret'] : '';
        }
        else
        { Log::error("Cannot find specified service endpoint for $name - check eos-mc connections!"); }

        $timeout = (float) SettingsSchema::fetch('Diagnostics.hardRequestTimeoutSeconds');
        $this->slow_threshold = (float) SettingsSchema::fetch('Diagnostics.slowResponseThresholdSeconds');

        $this->guzzle_client = new Client([
            'timeout' => $timeout,
        ]);
        $this->guzzle_options = [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json']
        ];
    }

    // append the $uri to our service url, tolerant of missing leading /
    private function extended_url( $uri )
    {
        $full_url = $this->service_url;
        if( $uri && ($uri != '') && (substr(0,1,$uri) != '/') )
        { $full_url .= '/' . $uri; }
        else
        { $full_url .= $uri; }
        return $full_url;
    }

    /**
     * facades for post, put, get, delete corresponding to Guzzle calls. These are identical to Guzzle except that we leave out
     * the base URL (since we are service-specific). Rather than throwing exceptions, any errors will be returned in the status_code
     *
     * @param $uri - e.g. '/api/myobject/2'
     * @param $options - any Guzzle options
     * @return array ['status_code', 'body', 'response_time']
     */
    public function get( $uri, $options )
    {
        return $this->send_request($this->guzzle_client, 'GET', $this->extended_url($uri), null, $options, false);
    }

    /**
     * @param $uri- e.g. '/api/myobject/2'
     * @param $data - data to be json_encoded
     * @param $options - any Guzzle options
     * @return array ['status_code', 'body', 'response_time']
     */
    public function post( $uri, $data, $options )
    {
        return $this->send_request($this->guzzle_client, 'POST', $this->extended_url($uri), $data, $options, false);
    }

    /**
     * @param $uri- e.g. '/api/myobject/2'
     * @param $data - data to be json_encoded
     * @param $options - any Guzzle options
     * @return array ['status_code', 'body', 'response_time']
     */
    public function put( $uri, $data, $options )
    {
        return $this->send_request($this->guzzle_client, 'PUT', $this->extended_url($uri), $data, $options, false);
    }

    /**
     * @param $uri- e.g. '/api/myobject/2'
     * @param $options - any Guzzle options
     * @return array ['status_code', 'body', 'response_time']
     */
    public function delete( $uri, $options )
    {
        return $this->send_request($this->guzzle_client, 'DELETE', $this->extended_url($uri), null, $options, false);
    }

    // don't actually send, just log to show we have the right service parameters.
    public function test()
    {
        $this->assign_transaction_id();
        $this->log('TEST');
    }

    /**
     * @param $method - GET/PUT/POST/DELETE
     * @param null $elapsed_time
     */
    private function log( $method, $elapsed_time = null )
    {
        $do_logging = SettingsSchema::fetch('Diagnostics.logOutbound') == "yes";
        if( $do_logging )
        {
            $trace = new ApiTraceLogger();
            $token_value = json_decode(self::$auth_header, true);
            $player_id = isset($token_value['player']['registrar_id']) ?
                $token_value['player']['registrar_id'] : null;
            $agent_id = isset($token_value['agent']['agent_id']) ?
                $token_value['agent']['agent_id'] : null;
            $trace->info('OUT('.$method.'): ' . $this->service_url . ' TID:' . self::$transaction_id .
                ($player_id ? ' Player ' . $player_id : '') .
                ($agent_id ? ' Agent ' . $agent_id : '') .
                ($elapsed_time ? ' in '.$elapsed_time.' sec' : ''));
        }
    }

    /**
     * TID will look like "mrb:qa:GUID-STRING"
     * or for prod "mrb::GUID-STRING"
     * we will pass through any TID we have received in middleware.
     */
    private function assign_transaction_id()
    {
        if( ! self::$transaction_id )
        { self::$transaction_id = uniqid(config('app.install_prefix')); }
    }

    /**
     * The internal Guzzle wrapper implementation.
     * @param $client - our Guzzle client
     * @param $method - GET, PUT, POST, DELETE
     * @param $url - fully qualified URL
     * @param $data - any body data, will be json_encoded
     * @param $options - correspond to Guzzle options
     * @param $debug_headers - set true for detailed trace - use with caution
     * @return array ['status_code', 'body', 'response_time']
     */
    private function send_request($client, $method, $url, $data, &$options, $debug_headers)
    {
        // prep the tap middleware if we want to use it for debugging.
        $clientHandler = $client->getConfig('handler');
        $trace = new ApiTraceLogger();
        $tapMiddleware = Middleware::tap(function ($request) use ($trace) {
            foreach ($request->getHeaders() as $name => $values)
            { $trace->info($name . ': ' . implode(', ', $values)); }
        });

        // collect the response time stats
        $options['on_stats'] = function (TransferStats $stats) use (&$time)
        { $elapsed = $stats->getTransferTime(); };

        $elapsed = null;
        $body = null;
        $status_code = 500;
        if ($debug_headers)
        { $options = array_merge($options, ['handler' => $tapMiddleware($clientHandler)]); }

        try {
            switch ($method)
            {
                case "get":
                    $response = $client->get($url, $options);
                    break;
                case "post":
                    $response = $client->post($url,
                        array_merge($options, ['body' => json_encode($data)]));
                    break;
                case "put":
                    $response = $client->put($url,
                        array_merge($options, ['body' => json_encode($data)]));
                    break;
                case "delete":
                    $response = $client->delete($url, $options);
                    break;
                default:
                    $response = $client->get($url, $options);
            }
            $body = json_decode($response->getBody());
            $status_code = $response->getStatusCode();
        }
        catch ( ServerException $e )
        {
            $trace->error('Guzzle Server Exception, TID:' . self::$transaction_id .' for service: ' . $this->service_url . ': ' . $e->getMessage());
            $body = $e->getMessage();
        }
        catch ( ClientException $e )
        {
            //todo: should we auto retry on a 503?
            if ( $e->hasResponse() )
            {
                $exception = (string)$e->getResponse()->getBody();
                $body = json_decode($exception);
                $status_code = $e->getCode();
            }
            else
            {
                $trace->error('Guzzle Client Exception, TID:' . self::$transaction_id .' for service: ' . $this->service_url . ': ' . $e->getMessage());
                $body = $e->getMessage();
            }
        }

        $this->log($method, $elapsed);
        if( $elapsed >= $this->slow_threshold )
        {
            //todo: implement circuit breaker
            $trace->warning("Slow response, TID:" . self::$transaction_id .
                ' for service: ' . $this->service_url . ': '. $elapsed . ' secs');
        }
        return ['status_code' => $status_code, 'body' => $body, 'response_time' => $elapsed];
    }

}

class CheckProcessorService extends EOSService
{
    public function __construct( )
    {
        parent::__construct('Check Processor');
    }
    // basic API send methods are inherited, but here we can
    // add any other custom service methods.
}
