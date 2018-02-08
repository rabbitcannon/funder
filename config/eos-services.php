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
                'class' => 'App\CheckProcessor',
                'connections' => [
                    'outbound' => [
                        'url' => env('CP_BASE_URI'),
                        'authentication' => 'oauth',
                        'clientid' => env('CP_CLIENT_ID'),
                        'clientsecret' => env('CP_CLIENT_SECRET'),
                    ],
                ],
             ]
       ]      
];

