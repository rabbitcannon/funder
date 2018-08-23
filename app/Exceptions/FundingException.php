<?php
namespace App\Exceptions;

use Eos\Common\Exceptions\EosException;

/**
 * Any exception you choose to define can have one or several sub-messages/codes. These
 * codes (mnemonics) will be returned in the API response, should your exception not be
 * internally caught/handled. Any array of parameters you pass will also be returned in
 * the API response, allowing a client to compose a meaningful localized error message.
 * As an example:
 *  throw new SampleException( '_MISSING_INPUT',['attrib'=>'foo'],$e )
 * would return JSON:
 * {'code':'_MISSING_INPUT',
 *  'message':'Input attribute not found: foo',
 *  'data':{'attrib'=>'foo'},
 *  'extended': <optional chained exception $e>}
 * and, unless this is in your Handler $dontReport list, would also log the message
 * to the laravel.log.
 *
 * Class SampleException
 * @package App\Exceptions
 */
class FundingException extends EosException {

    public function __construct( $mnemonic='', $parameters = [], Throwable $previous = NULL )
    {
        $this->init( $mnemonic, $parameters, [
            '_AUTHERROR' => 'Login Authentication failed: <message>.',
        ]);

        parent::__construct( $mnemonic, $parameters, $previous );
    }
}
