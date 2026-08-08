<?php

class Course {

    public $name;
    public $description;
    public $image;

    public function __construct($name, $description, $image) {
        $this->name = $name;
        $this->image = $image;
        $this->description = $description;
    }

}
