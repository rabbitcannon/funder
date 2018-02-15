<?php

namespace Eos\Common;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;
use Log;
use Exception;
use Eos\Common\EOSService;

class CheckProcessor extends EOSService
{
    public function __construct( )
    {
        parent::__construct('Check Processor');
    }

    /**
     * Make an api call to CheckProcessor microservice to generate a cheque for the given ChequeRequest entry.
     * As the cheque won't be generated immediately, CheckProcessor will accept the request and respond with a reference.
     * Update this reference in the ChequeRequest table and update the state.
     * If the request fails update the ChequeRequest state accordingly.
     * Incase of timeout just return cheque_request.
     *
     * @param ChequeRequest $cheque_request
     * @return $cheque_request
     * @throws Exception
     */
    public function requestCheque( ChequeRequest $cheque_request )
    {
        $player = $cheque_request->player;
        $this->setAuthHeader( $player );
        try
        {
            $data = [ 'request_amount' => $cheque_request->amount,
                      'wallet_transaction_id' => $cheque_request->global_txn_id,
                      'request_source' => 'Wallet',
                      'description'   => "Request $cheque_request->amount cents cheque for player $player->registrar_id" ];

            $response = $this->sendRequest( 'POST', '/api/checkRequest', $data );

            if( $response['status_code'] == 200 )
            { $cheque_request->pending()->save(); }
            else
            {
                Log::error("Cheque request failure: Http {$response['status_code']}: " . json_encode( $response['body'] ) );
                $cheque_request->failed()->save();
                return $cheque_request;
            }
        }
        catch( Exception $e )
        {
            Log::alert("Unknown failure contacting CheckProcesser: " . $e->getMessage() );
            $cheque_request->failed()->save();
            throw $e;
        }
        return $cheque_request;
    }

    /**
     * Api call to CheckProcessor to get cheque number status
     * Pass in the reference_id recieved from CheckProcessor to get the cheque number.
     * If the cheque is generated, CheckProcessor will return the checkNumber.
     * Upon receiving the checkNumber update the checkNumber , the state to complete and return the ChequeRequest object
     * If the cheque number is not generated then just return ChequeRequest object
     * TODO need to handle other status as well
     *
     * @param ChequeRequest $cheque_request
     * @return ChequeRequest
     * @throws Exception
     */
    public function requestChequeFulfilmentStatus( ChequeRequest $cheque_request )
    {
        $player = $cheque_request->player;
        $this->setAuthHeader( $player );
        try
        {
            $response = $this->sendRequest( 'GET', "/api/checkRequest/$cheque_request->global_txn_id" );
            $remote_check_request = $response['body'];

            //If we get a check number we save it. If they can't find our request or they failed it we mark ours
            //failed as trying again won't help. Other errors are ignored which leaves this request open and it
            //will be retried. The expectation is that non-fatal errors will resolve. 
            if( $response['status_code'] == 200 && ! empty( $remote_check_request->check_number ) )
            {
                $cheque_request->cheque_number = $remote_check_request->check_number;
                $cheque_request->cheque_datetime = $remote_check_request->check_date;
                $cheque_request->success()->save();
                return $cheque_request;
            }
            elseif( $response['status_code'] == 404 ||
                    ($response['status_code'] == 200 && $result['status'] == 'Failed') )
            {
                $cheque_request->failed()->save();
                Log::alert("Fulfilment failed for : $cheque_request->global_txn_id HTTP: {$response['status_code']}" );
                return $cheque_request;
            }
            return $cheque_request;
        }
        catch( Exception $e )
        {
            Log::alert("Unknown failure contacting CheckProcesser: " . $e->getMessage() );
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param ChequeRequest $cheque_request
     * @return ChequeRequest|bool
     * @throws Exception
     * Cancel an unfulfilled cheque_request
     */
    public function cancelChequeRequest( ChequeRequest $cheque_request )
    {
        $settings = Setting::get( 'checkprocessor' );
        $path = '/api/checkprocessor/';
        try
        {
            $path = $path.$cheque_request->reference_id;
            $data = [ 'reference' => $cheque_request->reference_id , 'description' => 'cancel referenceId '.$cheque_request->reference_id ];
            $data = json_encode( $data );
            $response = $this->client->delete( $path, ['json' => $data] );
            $result = json_decode( $response->getBody(), true );

            if( $response->getStatusCode() == 200 )
            {
                //Should it not be cancelled state, which makes more sense need to revisit
                $cheque_request->failed()->save();
                return $cheque_request;
            }
            elseif( $response->getStatusCode() == 404 )
            {
                Log::alert('Invalid reference_id : ' . $cheque_request->reference_id . ' msg : ', $result );
                return $cheque_request;
            }
        }
        catch( Exception $e )
        {
            Log::alert("Unknown failure contacting CheckProcesser: " . $e->getMessage() );
            throw new Exception($e->getMessage());
        }
    }
}