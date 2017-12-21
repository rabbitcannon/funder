<?php

namespace App;


class AuthPlayer
{
    public $firstname;
    public $lastname;
    public $email;
    public $registrar_id;
    public $state;
    // registrar_id is the only required input; null is an invalid player
    public function __construct( $inputs )
    {
        $this->registrar_id = $inputs['registrar_id'];
        $this->firstname = isset($inputs['firstname']) ? $inputs['firstname'] : '';
        $this->lastname = isset($inputs['lastname']) ? $inputs['lastname'] : '';
        $this->email = isset($inputs['email']) ? $inputs['email'] : '';
        $this->state = isset($inputs['state']) ? $inputs['state'] : 0;
    }

    public function valid() {
        return $this->registrar_id != null;
    }
}
