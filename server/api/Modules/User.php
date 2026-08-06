<?php

class User {

    protected $email;
    protected $pass;

    public function getEmail() {
        return $this->email;
    }

    public function getPass() {
        return $this->pass;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPass($pass) {
        $this->pass = $pass;
    }

}
