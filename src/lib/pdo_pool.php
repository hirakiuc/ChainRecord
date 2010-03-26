<?php

class PdoPool{

    private $instance = null;

    private $config = null;

    private $pool = null;

    private function __construct($path){
        $this->pool = array();

        // TODO yaml load and set $this->config;

        return $this;
    }

    public static function init($path){
        $this->instance = new Config($path);
        return $this;
    }

    public static function getInstance(){
        return $this->instance;
    }

    // TODO HowTo Implement getter Methods.
}


?>
