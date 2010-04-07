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

        foreach($this->primary_key as $pkey){
            if(!in_array($pkey, $this->column_names, true)){
                $msg = "No Such property found: ".$pkey;
                throw new NoSuchPropertyError($msg);
            }
        }
    }


    //--------------------------------------------
    /**
     *
     */
    public function create($ary){ 
        foreach($ary as $key => $v){ 
            if(!$this->is_column_exist($key)){
                $msg = $key." column is not defined.";
                throw new NoSuchPropertyError($msg);
            } 
            $this->vals[$key]["value"] = $v; 
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
     * $ary(
     *  "cond" => array("condition", array(params1,..)),
     *  "order" => order_by_string
     *  "limit" => number or array(limit, offset),
     * )
     */
    public function find($ary){
        $params = array();

        $query = "SELECT * FROM ".$this->table_name." ";

        $cond = "";
        if(isset($ary["cond"])){
            $v = $ary["cond"];

            $cond.= "WHERE ".$v[0];
            if(count($v) > 1){
                array_shift($v);
                $params = $v;
            }
        }
        
        if($this->is_pkey_set()){
            if(strlen($cond) > 0){
                $cond.= " AND ";
            }else{
                $cond = "WHERE ";
            }
            $v = $this->get_pkey_cond();

            $cond.= $v["cond"];
            $params = array_merge($params, $v["params"]);
        }

        if(strlen($cond) > 0){
            $query.= $cond;
        }

        if(isset($ary["order"])){
            $query.= " ORDER BY ".$ary["order"]." ";
        }
        
        if(isset($ary["limit"])){
            if(is_number($ary["limit"])){
                $query.= " LIMIT ".$ary["limit"]." ";
            }else{
                $query.= " LIMIT " .$ary["limit"][0];
                $query.= " OFFSET ".$ary["limit"][1];
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

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $clazz = new ReflectionClass(get_class($this));

            $objs = array();
            foreach($rows as $row){
                $v = $clazz->newInstance();
                array_push($objs, $v->create($row));
            }

            return new ChainTube($objs); 

        }catch(Exception $e){
            throw $e;
        } 
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
        $params = array();

        // accord arg value priority over $this property.
        if(isset($ary["vals"])){
            foreach($ary["vals"] as $name => $v){ 
                if(!$this->is_column_exist($name)){
                    $msg = $name." column is not defined.";
                    throw new NoSuchPropertyError($msg);
                } 
                $this->vals[$name]["value"] = $v;
                $this->vals[$name]["is_updated"] = true; 
            }
        }

        // gather updated column value property.
        $updated_props = array(); 
        foreach($this->vals as $key => $v){
            if($v["is_updated"]){
                $updated_columns[$key] = $v["value"];
            }
        }

        $query = "UPDATE ".$this->table_name." SET ";

        $columns = array();
        foreach($updated_props as $name => $v){
            array_push($columns, $name. " = ? ");
            array_push($params, $v);
        }
        $query.= implode($columns, ",");

        $cond = "";
        if(isset($ary["cond"])){
            $v = $ary["cond"];

            $cond.= "WHERE ".$v[0];
            if(count($v) > 1){
                array_shift($v);
                $params = $v;
            }
        }

        if($this->is_pkey_set()){
            if(strlen($cond) > 0){
                $cond.= " AND ";
            }else{
                $cond = "WHERE ";
            }
            $v = $this->get_pkey_cond();

            $cond.= $v["cond"];
            $params = array_merge($params, $v["params"]);
        }

        if(strlen($cond) > 0){
            $query.= $cond;
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

    /**
     * $ary(
     *  "cond" => array("condition", array()),
     * )
     */
    public function destroy($ary = array()){
        $params = array();

        $query = "DELETE FROM ". $this->table_name." ";

        $cond = "";
        if(isset($ary["cond"])){
            $v = $ary["cond"];

            $cond.= "WHERE ".$v[0];
            if(count($v) > 1){
                array_shift($v);
                $params = $v;
            }
        }

        // add self pkey condition if $this model has primary key values.
        // ("has" means "not null value")
        if($this->is_pkey_set()){
            if(strlen($cond) > 0){
                $cond.= " AND ";
            }else{
                $cond = "WHERE ";
            } 
            $v = $this->get_pkey_cond();

            $cond.= $v["cond"];
            $params = array_merge($params, $v["params"]);
        }

        if(strlen($cond) > 0){
            $query.= $cond;
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
            throw new NoSuchPropertyError($msg);
        }    
        $v = $this->vals[$name]; 
        return $v["value"];
    }

    function __set($name, $value){
        if(!$this->is_column_exist($name)){
            $msg = $name." column is not defined.";
            throw new NoSuchPropertyError($msg);
        } 
        $v = $this->vals[$name];
        $v["value"] = $value;
        $v["is_updated"] = true;
    }

    function __call($name, $parameters){

    }

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

    protected function is_pkey_set(){
        if(empty($this->primary_key)){
            return false;
        }

        foreach($this->primary_key as $key_name){
            $v = $this->vals[$key_name]["value"];
            if(is_null($v)){
                return fasle;
            }
        }

        return true;
    }

    protected function get_pkey_cond(){
        $params = array(); 

        $v = array();
        foreach($this->primary_key as $pkey){
            array_push($v, $pkey." = ? ");
            array_push($params, $this->vals[$pkey]["value"]);
        }

        $cond = " (". implode($v, " AND ") . ") "; 

        return array(
            "cond"   => $cond,
            "params" => $params
        );
    } 
}

/* vim: set expandtab sw=4 st=4 ts=4 : */
?>
