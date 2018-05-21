<?php

return [

    /*
     |--------------------------------------------------------------------------
     | eos.services.value  
     |--------------------------------------------------------------------------
     |
     | This is the default setting of the eos services configuration as defined in the eos.php
     | schema definition.
     */
    'services' => [
        'check-processor' => [
            'name' => 'Check Processor',
            'class' => 'Eos\Common\CheckProcessorService',
            'connections' => [
                'outbound' => [
                    'url' => env('CP_BASE_URI'),
                    'authentication' => 'oauth',
                    'clientid' => env('CP_CLIENT_ID'),
                    'clientsecret' => env('CP_CLIENT_SECRET'),
                ],
            ],
        ],
        'eos-wallet' => [
            'name' => 'EOS Wallet',
            'class' => 'App\EosWalletService', /*todo: replace with Eos\Common service */
            'connections' => [
                'outbound' => [
                    'url' => env('WALLET_BASE_URI'),
                    'authentication' => 'oauth',
                    'clientid' => env('WALLET_CLIENT_ID'),
                    'clientsecret' => env('WALLET_CLIENT_SECRET'),
                ],
            ],
        ],
    ]
];

