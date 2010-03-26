<?php

require_once "lib/chain_tube.php";
require_once "lib/errors.php";
require_once "lib/config.php";
require_once "lib/pdo_pool.php";


class ChainRecord {

    protected $config = null;

    protected $pdo_config = null;

    //--------------------------------------------
    public static function init($ary){
        $config     = Config::init($ary['config']);
        $pdo_config = PdoPool::init($ary['pdo_config']);
    }

    //--------------------------------------------
    public function __construct(){

    }


    //--------------------------------------------
    public function create($ary){
        return $this;
    }


    //--------------------------------------------
    public function save(){
        return false;
    }

    public function find($ary){
        return new ChainTube(array());
    }

    public function update($new_vals, $ary){
        return 0;
    }

    public function destroy($ary){
        return 0;
    }

    //--------------------------------------------
    function __get($name){

    }

    function __set($name, $value){

    }

    function __call($name, $parameters){

    }
}

/* vim: set expandtab sw=4 st=4 ts=4 : */
?>
