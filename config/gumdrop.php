<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    "schema" => ['type' => 'group',
          "fields"=> [
               "color" => ["type"=>"text","value"=>"red"],
               "number" => ["type"=>"number","valid"=>["min"=>0,"max"=>1000000]],
              "number2" => ["type"=>"number","valid"=>["min"=>0,"max"=>1000000]],
               "count" => ["type"=>"number","value"=>1,"valid"=>["min"=>1,"max"=>10]],
               "notify" => ["type"=>"group",
                            "fields"=> [
                                "playerid" => ["type"=>"text"],
                                "by" => ["type"=>"enum","valid"=>["email","push","messagecenter"],"value"=>"email"]
                            ] ],
               "games" => ["type"=>"multigroup", 
                           "extensible"=>true,
                           "fields"=> [
                               "name" => ["type"=>"text","valid"=>["sample"=>"Powerball"]],
                               "desc" => ["type"=>"text","valid"=>["sample"=>"A multi-state draw game"]],
                               "effective" => ["type"=>"text","valid"=>["regex"=>"\d{4}-\d{2}-\d{2}","sample"=>"YYYY-MM-DD"]]],
                           "value" => [
                               "sample-game" => [
                                   "name" => "A Sample Game",
                                   "desc" => "A sample game",
                                   "effective" => "2018-01-01"
                                   ],
                               ]
                           ],
               'shapes' => ['type' => 'multigroup', 'extensible' => true,
                    'fields' => [
                        'color' => ['type'=>'enum','valid'=>['none','red','blue','yellow']],
                        'shape' => ['type'=>'oneof', 'fields' => [
                            'square' => ['type'=>'group','fields'=>[
                                'centerX' => ['type'=>'number','decimal'=>true,'value'=>0],
                                'centerY' => ['type'=>'number','decimal'=>true,'value'=>0],
                                'sideLength' => ['type'=>'number','decimal'=>true,'value'=>1]
                            ]],
                            'circle' => ['type'=>'group','fields'=>[
                                'centerX' => ['type'=>'number','decimal'=>true,'value'=>0],
                                'centerY' => ['type'=>'number','decimal'=>true,'value'=>0],
                                'radius' => ['type'=>'number','decimal'=>true,'value'=>1]
                            ]],
                            'ellipse' => ['type'=>'group','fields'=>[
                                'focus1X' => ['type'=>'number','decimal'=>true,'value'=>-1],
                                'focus1Y' => ['type'=>'number','decimal'=>true,'value'=>0],
                                'focus2X' => ['type'=>'number','decimal'=>true,'value'=>1],
                                'focus2Y' => ['type'=>'number','decimal'=>true,'value'=>0],
                                'sumRadius' => ['type'=>'number','decimal'=>true,'value'=>1.5]
                            ]]
                        ]]
                    ],
                   'value' => []
                ]
             ]
      ]

];
