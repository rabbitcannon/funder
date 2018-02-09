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
    
    /*
     |--------------------------------------------------------------------------
     | Service Name
     |--------------------------------------------------------------------------
     |
     | Replace with the full name of your service.
     |
     */
    
    'service_name' => 'Dev Common',
    
    /*
     |--------------------------------------------------------------------------
     | Schema Namespaces
     |--------------------------------------------------------------------------
     |
     | A comma separated list of config namespaces. Each name on this list refers to a
     | /config/<name>.php file that must define a 'schema' tag. You may define other
     | STATIC application configurations in this file.
     |
     */
    
    'schema_namespaces' => ['eos'],
    
];

