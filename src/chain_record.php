<?php

require_once "lib/chain_tube.php";
require_once "lib/errors.php";
require_once "lib/config.php";
require_once "lib/pdo_pool.php";
require_once "lib/meta_driver_factory.php";
require_once "lib/util.php";

class ChainRecord {

    /**  */
    protected $db_key = null;

    /**  */
    protected $config = null;

    /**  */
    protected $pdo_config = null; 

    /**
     * array(
     *  "name"     : name_string
     *  "type"     : data_type_string,
     *  "nullable" : true or false,
     *  "precision": integer
     * )
     */
    protected $props = array();

    /**
     * array(
     *   column_name : array( "value" : value, "is_updated" : true or false ),
     *   ...
     * )
     */
    protected $vals = array();

    //--------------------------------------------
    /**
     *
     */
    public static function init($path){
        Config::init($path);
    }

    //--------------------------------------------
    /**
     *
     */
    public function __construct(){
        $obj = PdoPool::getInstance();
        $pdo_info = $obj->getPdoInfo();

        $this->db_key     = $pdo_info["key"];
        $this->pdo        = $pdo_info["pdo"];
        $this->pdo_config = $pdo_info["config"];

        $this->table_name = Util::tabilize(get_class($this));

        $meta_driver = MetaDriverFactory::getInstance($this->pdo, $this);
        // TODO implement set $this->props
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
