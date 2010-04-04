<?php

require_once "lib/chain_tube.php";
require_once "lib/errors.php";
require_once "lib/config.php";
require_once "lib/pdo_pool.php";
require_once "lib/meta_driver_factory.php";
require_once "lib/inflector.php";

class ChainRecord {
    
    /**  */
    protected $primary_key = array(); 

    /**  */
    protected $db_key = null;

    /**  */
    protected $config = null;

    /**  */
    protected $pdo = null;

    /**  */
    protected $pdo_config = null; 

    /**  */
    protected $table_name = null;

    /**
     * array(
     *  column_name => 
     *      array(
     *          "type"     : data_type_string,
     *          "nullable" : true or false,
     *          "length"   : character_length_integer
     *      ),
     *  ...
     * )
     */
    protected $props = array();

    /**
     * array(column_1, column_2, ...)
     */
    protected $column_names = array();

    /**
     * array(
     *   column_name => array( "value" : value, "is_updated" : true or false ),
     *   ...
     * )
     */
    protected $vals = array();

    //--------------------------------------------
    /**
     * Initialize ChainRecord Library method.
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

        $this->table_name = SInflector::tableize(get_class($this));

        $meta_driver = MetaDriverFactory::getInstance();
        $this->props = $meta_driver->getColumns($this->pdo, $this->table_name);

        $this->column_names = array_keys($this->props);

        $this->vals = array();
        foreach($this->column_names as $name){
            $this->vals[$name] = array(
                "value"      => null,
                "is_updated" => false
            );
        } 
    }


    //--------------------------------------------
    /**
     *
     */
    public function create($ary){ 
        foreach($ary as $key => $val){
            $this->$key = $val;
        }

        foreach($this->vals as $name => $v){
            $v["is_updated"] = false;
        } 
        return $this;
    }


    //--------------------------------------------
    /**
     *
     */
    public function save(){ 
        $params = array();

        $query = "INSERT INTO ".$this->table_name." ";
        $query.= "(". implode($this->column_names, ",").") ";
        $query.= "VALUES ";

        $marker = array();
        foreach($this->vals as $key => $v){
            array_push($params, $v["value"]); 
            array_push($marker, "?");
        }

        $query.= "(". implode($marker, ","). ")";

        try{
            $stmt = $this->pdo->prepare($query);
            if(is_null($stmt)){
                throw new DBError($this->get_errmsg());
            }

            if(!$stmt->execute($params)){
                throw new DBError($this->get_errmsg());
            }

            return true;
        }catch(Exception $e){
            throw $e;
        } 
    }

    /**
     *
     */
    public function find($ary){
        // SQL:select
        return new ChainTube(array());
    }

    /**
     * $ary(
     *  "vals" => array(
     *      "column1" => $phpvalue,
     *      ...
     *  ), 
     *  "cond" => array("condition", array())
     * )
     */
    public function update($ary){
        // SQL:update
        $params = array();



        return 0;
    }

    /**
     * $ary(
     *  "cond" => array("condition", array()),
     * )
     */
    public function destroy($ary = array()){
        $params = array();

        $query = "DELETE FROM ". $this->table_name." ";
        if(isset($ary["cond"])){
            $v = $ary["cond"];

            $query.= "WHERE ".$v[0];
            if(count($v) > 1){
                array_shift($v);
                $params = $v;
            }
        }

        try{
            $stmt = $this->pdo->prepare($query);
            if(is_null($stmt)){
                throw new DBError($this->get_errmsg());
            }

            if(!$stmt->execute($params)){
                throw new DBError($this->get_errmsg());
            }

            return $stmt->rowCount(); 
        }catch(Exception $e){
            throw $e;
        } 
    }

    //--------------------------------------------
    function __get($name){
        if(!$this->is_column_exist($name)){
            $msg = $name." column is not defined.";
            throw new ColumnNotFoundError($msg);
        }    
        $v = $this->vals[$name]; 
        return $v["value"];
    }

    function __set($name, $value){
        if(!$this->is_column_exist($name)){
            $msg = $name." column is not defined.";
            throw new ColumnNotFoundError($msg);
        } 
        $v = $this->vals[$name];
        $v["value"] = $value;
        $v["is_updated"] = true;
    }

    function __call($name, $parameters){

    }

//    private function check_column_exist($name){
//        if($this->is_column_exist($name)){
//            $msg = "No Such Property exist: " .$name;
//            throw new NoSuchPropertyError($msg);
//        }
//    }

    protected function get_errmsg(){
        $err = $this->pdo->errorInfo();
        if($err[0]){
            return "pdo has no error code.";
        }else{
            return "[".$err[0]."] ".$err[2]."(".$err[1].")";
        }
    }

    protected function is_column_exist($name){
        return array_key_exists($name, $this->props);
    }
}

/* vim: set expandtab sw=4 st=4 ts=4 : */
?>
