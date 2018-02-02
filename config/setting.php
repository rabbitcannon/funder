<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Setting Schema
    |--------------------------------------------------------------------------
    |
    | The setting schema has one required key (namespaces) and one optional key (groups).
    | Use namespaces to list your application's default namespace and the namespace of all
    | installed components (if any).
    |
    | The groups key is used to define parent child groups like jurisdictions and sub-jurisdictions.
    | An example of the (unsupported) syntax is shown below for sub-jurisdictions.
    */

    'schema' => [
        'namespaces' => ['eos', 'gumdrop'],
        
        /*
         * 'groups' => [ 'alc' => [
         *                  'global' => true,
         *                  'path' => ['player' => 'subjurisdiction'], ],
         *               'ns' => [],
         *               'pe' => [], ]
         */
    ]
    
];
