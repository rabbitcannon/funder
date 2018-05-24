<?php

return [

    /*
     |--------------------------------------------------------------------------
     | eos.services.value  
     |--------------------------------------------------------------------------
     |
     | This is the default setting of the eos services configuration as defined in the eos.php
     | schema definition.
     | We need EmCee in order to make Scheduler notifications work.
     | Wallet is provided mostly as an example, but we have a peer test embedded
     | in the GumdropController just to demonstrate peer service auth and logging.
     */
    'services' => [
        'eos-emcee' => [
            'name' => 'EOS EmCee',
            'connections' => [
                'outbound' => [
                    'url' => env('MC_BASE_URI'),
                    'authentication' => 'none'
                ]
            ]
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

