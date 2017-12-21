<?php

namespace App;


class AuthAgent
{
    public $firstname;
    public $lastname;
    public $email;
    public $agent_id;
    // agent_id is the only required input; null is an invalid agent
    public function __construct( $inputs )
    {
        $this->agent_id = $inputs['agent_id'];
        $this->firstname = isset($inputs['firstname']) ? $inputs['firstname'] : '';
        $this->lastname = isset($inputs['lastname']) ? $inputs['lastname'] : '';
        $this->email = isset($inputs['email']) ? $inputs['email'] : '';
    }

    public function valid() {
        return $this->agent_id != null;
    }
}
