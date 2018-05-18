<?php
/**
 * The Archivist (see eos-common wiki 'Archivist') needs to know what tables should
 * have old rows archived and pruned. If there are dependent records (fkey references)
 * they must be indicated in the 'depends' clause.
 */
return [
    'shards' => [
        \App\Gumdrop::class => [
            'retention' => env( 'GUMDROP_RETENTION_QUANTUM', 'year' ),
            'span' => env( 'GUMDROP_RETENTION_SPAN', 1 ),
            'depends' => [ \App\Player::class => [
                'retention' => env( 'PLAYER_RETENTION_QUANTUM', 'year' ),
                'span' => env( 'PLAYER_RETENTION_SPAN', 1 ),
                ] ]
        ]
    ]
];
