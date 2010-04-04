<?php
require_once "errors.php";
require_once "metadriver/pgsql_meta_driver.php";
require_once "metadriver/mysql_meta_driver.php";

class MetaDriverFactory{
    /**  */
    private static $instance = null;

    /** 
     * array($table_name => $props,...)
     */
    private $cache = null;

    /**
     *
     */
    private function __construct(){
        $this->cache = array();
    }

    /**
     *
     */
    public static function getInstance(){
        if(is_null(MetaDriverFactory::$instance)){
            MetaDriverFactory::$instance = new MetaDriverFactory();
        }
        return MetaDriverFactory::$instance;
    }

    /**
     *
     */
    public function getColumns($pdo, $table_name){

        if($this->isCached($table_name)){
            return $this->cache[$table_name];
        }

        $meta_driver = null;
        $adapter = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if($adapter === "mysql"){
            $meta_driver = new MySQLMetaDriver();
        }else if($adapter === "pgsql"){
            $meta_driver = new PgSQLMetaDriver();
        }

        if(is_null($meta_driver)){
            $msg = "Not Supported Database:".$adapter;
            throw new NotSupportDbError($msg);
        }

        $props = $meta_driver->getColumns($pdo, $table_name);

        $this->addCache($table_name, $props);

        return $props;
    }

    /**
     *
     */
    private function isCached($table_name){
        return isset($this->cache[$table_name]);
    }

    /**
     *
     */
    private function addCache($table_name, $props){
        $this->cache[$table_name] = $props;
    }
}


abstract class MetaDriver {
    abstract public function getColumns($pdo, $table_name);
}

?>
