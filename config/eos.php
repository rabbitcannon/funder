<?php

return [

    /*
     |--------------------------------------------------------------------------
     | RUN
     |--------------------------------------------------------------------------
     |
     | The run and app_name should be set in .env. It is here for caching
     |
     */
    
    'run' => strtolower( env( 'RUN', 'zzz' ) ),
    'app_name' => strtolower( env( 'APP_NAME', 'default' ) ),
    
    /*
     |--------------------------------------------------------------------------
     | Setting Cache Only
     |--------------------------------------------------------------------------
     |
     | Use this to disable use of the setting_packs db table. This feature allows
     | loading settings from EOS configuration and storing in Laravel cache.
     |
     */
    
    'setting_cache_only' => env( 'SETTING_CACHE_ONLY', FALSE),

    /*
     |--------------------------------------------------------------------------
     | Service Name
     |--------------------------------------------------------------------------
     |
     | Replace with the full name of your service.
     |
     */

    'service_name' => 'Test',

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
    
    'schema_namespaces' => ['eos','gumdrop'],

    
];

