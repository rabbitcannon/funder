<?php

namespace App;

use Eos\Common\EOSService;

class EosWalletService extends EOSService
{
    public function __construct( )
    {
        parent::__construct('EOS Wallet', []);
    }

    public function fetchAccounts()
    {
        $response = $this->get('api/accounts');
    }
}