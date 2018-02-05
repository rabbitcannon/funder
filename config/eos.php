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
                     'services' => ['type' => 'multigroup', 'extensible' => true,
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
                                            'hardRequestTimeoutSeconds' => ['type' => 'number', 'value' => 10 ],
                                            'slowResponseThresholdSeconds' => ['type' => 'number', 'value' => 5],
                                       ]
                     ]
                 ]
    ],
    
];

