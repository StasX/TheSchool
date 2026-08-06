<?php

class Administrator {

    public $name;
    public $role;
    public $phone;
    public $email;
    public $password;
    public $image;

    public function __construct($name, $role, $phone, $email, $password, $image) {
        $this->name = $name;
        $this->role = $role;
        $this->phone = $phone;
        $this->email = $email;
        $this->password = $password;
        $this->image = $image;
    }

}
