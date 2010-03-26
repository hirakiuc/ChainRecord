<?php

class Config{

  private static $instance = null;

  private $config = null;

  private function __construct($path){
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
