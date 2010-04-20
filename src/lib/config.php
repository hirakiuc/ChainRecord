<?php
require_once "errors.php";
require_once "spyc.php";

class Config{

    /** */
    private static $instance = null;

    /** */
    public $config = null;

    /** 
     *
     */
    private function __construct($path){ 
        try{
            // TODO check $path is valid filepath?
            $this->config = Spyc::YAMLLoad($path); 
        }catch(Exception $e){
            throw new YamlLoadError(
                "failed to load yaml file:".$path);
        } 
    }

    /**
     *  Call First 
     */
    public static function init($path){
        Config::$instance = new Config($path);
        return Config::$instance;
    }

    /**
     *
     */
    public static function getInstance(){
        if(Config::$instance === null){
            throw new NotInitException("Not initialized");
        }
        return Config::$instance;
    } 
} 

?>
