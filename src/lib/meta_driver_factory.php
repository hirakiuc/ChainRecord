<?php

class MetaDriverFactory{

    private static $instance = null;

    private $cache = null;

    private function __construct(){
        $this->cache = array();
    }

    public static function getInstance(){
        if(is_null(MetaDriverFactory::$instance)){
            MetaDriverFactory::$instance = new MetaDriverFactory();
        }
        return MetaDriverFactory::$instance;
    }

    public function getTableColumns($pdo, $model){
        $table_name = $model->table_name;

        $meta_driver = null;
        $adapter = $model->pdo_config["adapter"];
        if($adapter === "mysql"){
            
        }else if($adapter === "postgresql"){

        }

        if(is_null($meta_driver)){
            $msg = "Not Supported Database:".$adapter;
            throw new NotSupportDbError($msg);
        }

    }
}

?>
