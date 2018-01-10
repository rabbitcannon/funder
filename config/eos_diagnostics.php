<?php
// service schema for EOS diagnostics
return [
    "Diagnostics" => ["type" =>"group","fields"=>[

        "logInbound" => ["type"=>"enum","value"=>"yes","valid"=>["yes","no"]],
        "logOutbound" => ["type"=>"enum","value"=>"yes","valid"=>["yes","no"]],
        "hardRequestTimeoutSeconds" => ["type"=>"number","value"=>"20","valid"=>["min"=>0,"max"=>600]],
        "slowResponseThresholdSeconds" => ["type"=>"number","value"=>"8","valid"=>["min"=>0,"max"=>100]],
        "circuitBreakAfterCountSlowResponses" => ["type"=>"number","value"=>"4","valid"=>["min"=>1,"max"=>100]],
        "recheckBrokenCircuitAfterSeconds" => ["type"=>"number","value"=>"60","valid"=>["min"=>1,"max"=>1000]],
        "retainTxIdsForSeconds" => ["type"=>"number","value"=>"3600","valid"=>["min"=>1,"max"=>10000]]
    ]]
];
