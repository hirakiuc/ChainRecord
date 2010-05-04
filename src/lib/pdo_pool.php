<?php
require_once "lib/errors.php";
require_once "lib/config.php";

class PdoPool{

    /** */
    private static $instance = null;

    /** */
    private $config = null;

    /** */
    private $pool = null;

    /**
     *
     */
    private function __construct(){
        $this->pool = array();

        $obj = Config::getInstance();
        $this->config = $obj->config["databases"];

        return $this;
    }

    /**
     *
     */
    public static function getInstance(){ 
        if(PdoPool::$instance == null){
            PdoPool::$instance = new PdoPool();
        }
        return PdoPool::$instance;
    }
   
    /**
     *
     */
    public function getPdoInfo($key = "default"){

        if(is_null($this->pool[$key])){ 
            if($key === "default"){
                $key = $this->config["default"];
            } 
            $config = $this->config[$key];

            $this->createPdo($key, $config);
        }

        $pdo_info = $this->pool[$key];

        return array(
            "key"   => $key,
            "pdo"   => $pdo_info["pdo"],
            "config"=> $pdo_info["config"]
        ); 
    }

    private function createPdo($key, $config){

        if(is_null($config)){
            throw new InvalidConfigError(
                "target database is not configured:". $key);
        }

        $pdo = null;
        if($config['adapter'] === "mysql"){
            $pdo = $this->getMySQLPdo($config);
        }else if ($config['adapter'] === "postgresql"){
            $pdo = $this->getPostgreSQLPdo($config);
        }else{
            throw new NotSupporDbError(
                "target adapter is not supported:"
                .$config['adapter']);
        }

        if(is_null($pdo)){
            throw new DBError(
                "Connection failed:("
                .$config["adapter"] . "-"
                .$config["host"]. ":". $config["port"]
                .")"
            );
        }

        $this->pool[$key] = array(
            "pdo"    => $pdo, 
            "config" => $config
        ); 
    }

    /**
     * Create PDO for MySQL
     */
    private function getMySQLPdo($config){
        // TODO check config
        // TODO support unix socket

        $ary = array();
        array_push($ary, "host=".$config["host"]);
        array_push($ary, "port=".$config["port"]);
        array_push($ary, "dbname=".$config["dbname"]);
        $dsn = "mysql:".implode($ary,";");

        return new PDO($dsn,$config["user"],$config["password"]);
    }

    /**
     * Create PDO for PostgreSQL
     */
    private function getPostgreSQLPdo($config){
        // TODO check config

        $ary = array();
        array_push($ary, "host=".$config["host"]);
        array_push($ary, "port=".$config["port"]);
        array_push($ary, "dbname=".$config["dbname"]);
        array_push($ary, "user=".$config["user"]);
        array_push($ary, "password=".$config["password"]);
        $dsn = "pgsql:".implode($ary," ");

        return new PDO($dsn);
    }
}


?>
