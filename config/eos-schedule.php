<?php

return [

    /*
     |--------------------------------------------------------------------------
     | eos.schedule.value
     |--------------------------------------------------------------------------
     |
     | This is the default setting of the eos schedule configuration as defined in the eos.php
     | schema definition.
     */
    'schedule' => [
            'fee' => [
                'when' => [
                    'daily' => ['timeofday' => '04:00']
                ],
                'what' => [
                    'commandtext' => 'giant:fee -v',
                    'purpose' => 'begin giant statement',
                    'expecteddurationseconds' => 4
                ]
            ],
            'fie' => [
                'when' => [
                    'daily' => ['timeofday' => '06:00']
                ],
                'what' => [
                    'commandtext' => 'giant:fie -v',
                    'purpose' => 'next giant statement',
                    'expecteddurationseconds' => 4
                ]
            ],
            'foe' => [
                'when' => [
                    'hourly' => ['atminute' => '05']
                ],
                'what' => [
                    'commandtext' => 'giant:foe -v',
                    'purpose' => 'third giant statement',
                    'expecteddurationseconds' => 2,
                    'outputlog' => 'logs/foe.log'
                ]
            ],
            'fum' => [
                'when' => [
                    'weekly' => ['dayofweek' => 'mondays','timeofday' => '02:00']
                ],
                'what' => [
                    'commandtext' => 'giant:fum -v',
                    'purpose' => 'final giant statement',
                    'expecteddurationseconds' => 2,
                    'nooverlap' => true
                ]
            ]
       ]      
];

