<?php

namespace App;

use Eos\Common\EOSService;

class EosWallet extends EOSService
{
    public function __construct( )
    {
        parent::__construct('EOS Wallet');
    }

}