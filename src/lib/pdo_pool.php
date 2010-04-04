<?php
require_once "errors.php";
require_once "config.php";

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
    public function getPdoInfo($key = null){

        if(is_null($key)){
            $obj = Config::getInstance();
            $config = $obj->config;

            if(is_null($key)){
                // set default databas key.
                $key = $config['databases']['default']; 
            }
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

        $db_config = $config['databases'][$key]; 

        if(is_null($db_config)){
            throw InvalidConfigError(
                "target database is not configured:". $key);
        }

        $pdo = null;
        if($db_config['adapter'] === "mysql"){
            $pdo = $this->getMySQLPdo($db_config);
        }else if ($db_config['adapter'] === "postgresql"){
            $pdo = $this->getPostgreSQLPdo($db_config);
        }else{
            throw new NotSupporDbError(
                "target adapter is not supported:"
                .$db_config['adapter']);
        }

        if(is_null($pdo)){
            throw new DBError(
                "Connection failed:("
                .$db_config["adapter"] . "-"
                .$db_config["host"]. ":". $db_config["port"]
                .")"
            );
        }

        $this->pool[$key] = array(
            "pdo"    => $pdo, 
            "config" => $db_config
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
