<?php
// service schema for EOS diagnostics
return [
    "Diagnostics" => ["type" =>"group","fields"=>[

        "logInbound" => ["type"=>"enum","value"=>"yes","valid"=>["yes","no"]],
        "logOutbound" => ["type"=>"enum","value"=>"yes","valid"=>["yes","no"]],
        "hardRequestTimeoutSeconds" => ["type"=>"number","valid"=>["min"=>0,"max"=>600]],
        "slowResponseThresholdSeconds" => ["type"=>"number","valid"=>["min"=>0,"max"=>100]],
        "circuitBreakAfterCountSlowResponses" => ["type"=>"number","valid"=>["min"=>1,"max"=>100]],
        "recheckBrokenCircuitAfterSeconds" => ["type"=>"number","valid"=>["min"=>1,"max"=>1000]],
        "retainTxIdsForSeconds" => ["type"=>"number","valid"=>["min"=>1,"max"=>10000]]
    ]]
];
