<?php

return [

    /*
     |--------------------------------------------------------------------------
     | RUN
     |--------------------------------------------------------------------------
     |
     | The run should be set in .env. It is here for caching
     |
     */
    
    'run' => strtolower( env( 'RUN', 'zzz' ) ),
    
    'schema' => ['type' => 'group',
                 'fields' => [
                     'services' => ['type' => 'multigroup', 'extensible' => true, 'hidden' => true,
                             'fields' => [
                                 'name' => ['type' => 'text', 'sample' => 'Micro Service'],
                                 'class' => ['type' => 'text', 'sample' => 'App\MyClass'],
                                 'connections' => ['type' => 'group',
                                       'fields' => [
                                           'outbound' => ['type' => 'group',
                                               'fields' => [
                                                   "url" => ["type"=>"text","sample"=>"http://path.to.service"],
                                                   "authentication" => ["type"=>"enum","valid"=>["oauth","apikey","none"]],
                                                   "clientid"=>["type"=>"text","sample"=>"20"],
                                                   "clientsecret"=>["type"=>"text","sample"=>"x35Y33Ab..."],
                                                   "oauthtoken"=>["type"=>"text"],
                                                   "apikey"=>["type"=>"text","sample"=>"t5E3Wx..."],
                                                   "apisecret"=>["type"=>"text","sample"=>"GG4rEw2X..."],
                                                   "apiversion"=>["type"=>"text","sample"=>"1"]
                                               ],
                                           ],
                                       ], 
                                 ], 
                             ], 
                             'value' => [
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
                     ],
        
                     'diagnostics' => ['type' => 'group',
                                        'fields' => [
                                            "logInbound" => ["type"=>"boolean","value"=>true],
                                            "logOutbound" => ["type"=>"boolean","value"=>true],
                                            "hardRequestTimeoutSeconds" => ["type"=>"number","value"=>"20","valid"=>["min"=>0,"max"=>600]],
                                            "slowResponseThresholdSeconds" => ["type"=>"number","value"=>"8","valid"=>["min"=>0,"max"=>100]],
                                            "circuitBreakAfterCountSlowResponses" => ["type"=>"number","value"=>"4","valid"=>["min"=>1,"max"=>100]],
                                            "recheckBrokenCircuitAfterSeconds" => ["type"=>"number","value"=>"60","valid"=>["min"=>1,"max"=>1000]],
                                            "retainTxIdsForSeconds" => ["type"=>"number","value"=>"3600","valid"=>["min"=>1,"max"=>10000]]
                                       ]
                     ]
                 ]
    ],
    
];

