<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\Endpoints;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Middleware;


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

    public function __construct($name)
    {
        $this->service_name = $name;
        $endpoints = Endpoints::GetEndpoints();
        foreach ($endpoints as $endpoint) {
            if ($endpoint['name'] == $name) {
                $this->service_url = $endpoint['url'];
                $this->auth_type = $endpoint['auth'];
                $this->client_id = isset($endpoint['client_id']) ? $endpoint['client_id'] : '';
                $this->client_secret = isset($endpoint['client_secret']) ? $endpoint['client_secret'] : '';
                break;
            }
        }
        $this->guzzle_client = new Client([
            'timeout' => 8.0,
        ]);
        $this->guzzle_options = [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json']
        ];


    }


    // a facade for post, put, get, delete corresponding to Guzzle calls. These are identical to Guzzle except that we leave out
    // the URL (since we are service-specific). Rather than throwing exceptions, any errors will be returned in the status_code
    //
    public function get($options)
    {
        return $this->send_request($this->guzzle_client, 'GET', $this->service_url, null, $options, false);
    }

    public function post($data, $options)
    {
        return $this->send_request($this->guzzle_client, 'POST', $this->service_url, $data, $options, false);
    }

    public function put($data, $options)
    {
        return $this->send_request($this->guzzle_client, 'PUT', $this->service_url, $data, $options, false);
    }

    public function delete($options)
    {
        return $this->send_request($this->guzzle_client, 'DELETE', $this->service_url, null, $options, false);
    }

    // don't actually send, just log to show we have the right service parameters.
    public function test()
    {
        $this->assign_transaction_id();
        $this->log('TEST');
    }

    private function log( $method, $elapsed_time = null )
    {
        if (config('app.eos_log_outbound')) {
            $token_value = json_decode(self::$auth_header, true);
            $player = isset($token_value['player']['registrar_id']) ?
                $token_value['player']['registrar_id'] : null;
            $agent = isset($token_value['agent']['agent_id']) ?
                $token_value['agent']['agent_id'] : null;
            Log::info('OUT('.$method.'): ' . $this->service_url . ' TID:' . self::$transaction_id .
                ($player ? ' Player ' . $player : '') .
                ($agent ? ' Agent ' . $agent : '') .
                ($elapsed_time ? ' in '.$elapsed_time.' sec' : ''));
        }
    }

    private function assign_transaction_id()
    {
        if (!self::$transaction_id) {
            self::$transaction_id = uniqid('');
        }
    }


    private function send_request($client, $method, $url, $data, &$options, $debug_headers)
    {
        // prep the tap middleware if we want to use it for debugging.
        $clientHandler = $client->getConfig('handler');
        $tapMiddleware = Middleware::tap(function ($request) {
            foreach ($request->getHeaders() as $name => $values) {
                Log::info($name . ': ' . implode(', ', $values));
            }
        });

        // collect the response time stats
        $options['on_stats'] = function (TransferStats $stats) use (&$time) {
            $time = $stats->getTransferTime();
        };

        $time = null;
        $body = null;
        $status_code = 500;
        if ($debug_headers) {
            $options = array_merge($options, ['handler' => $tapMiddleware($clientHandler)]);
        }

        try {
            switch ($method) {
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
        } catch (BadResponseException $e) {
            Log::error('Guzzle Server Exception, TID:' . self::$transaction_id .' for service: ' . $this->service_url . ': ' . $e->getMessage());
            $body = $e->getMessage();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $exception = (string)$e->getResponse()->getBody();
                $body = json_decode($exception);
                $status_code = $e->getCode();
            } else {
                Log::error('Guzzle Client Exception, TID:' . self::$transaction_id .' for service: ' . $this->service_url . ': ' . $e->getMessage());
                $body = $e->getMessage();
            }
        }

        $this->log($method, $time);
        return ['status_code' => $status_code, 'body' => $body, 'time' => $time];
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
