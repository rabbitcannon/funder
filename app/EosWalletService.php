<?php

namespace App;

use Eos\Common\EOSService;

class EosWalletService extends EOSService
{
    /**
     * EosWalletService constructor.
     * @throws \Eos\Common\Exceptions\EosException
     */
    public function __construct( )
    {
        parent::__construct('EOS Wallet', []);
    }

    public function fetchAccounts()
    {
        $response = $this->get('api/accounts');
    }
}