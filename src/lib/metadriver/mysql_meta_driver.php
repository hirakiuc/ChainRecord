<?php
require_once "lib/meta_driver_factory.php";
require_once "lib/errors.php";

class MySQLMetaDriver extends MetaDriver{ 
    public function getColumns($pdo, $table_name){
        $query = "SELECT ";
        $query.= "COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT,EXTRA, ";
        $query.= "CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, ORDINAL_POSITION ";
        $query.= "FROM information_schema.columns ";
        $query.= "WHERE table_name = ? ";
        $query.= "ORDER BY ORDINAL_POSITION";

        try{
            $stmt = $pdo->prepare($query);

            if(!$stmt->execute(array($table_name))){
                $msg = "[BUG] PgSQLMetaDriver request invalid query";
                throw new CRError($msg);
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($rows)){
                $msg = "table is not defined:(".$table_name.")";
                throw new TableNotFoundError($msg);
            }

            $props = array();
            foreach($rows as $row){
                $column = array();

                $name = $row['COLUMN_NAME'];
                $column['type'] = $row['DATA_TYPE'];
                $column['length'] = $row['CHARACTER_MAXIMUM_LENGTH'];
                $column['nullable'] = $row['IS_NULLABLE'];

                if(is_null($row['COLUMN_DEFAULT']) && $row['EXTRA'] !== "auto_increment"){
                    $column['default_exist'] = false;
                    $column['auto_increment'] = false;
                }else if(!is_null($row['COLUMN_DEFAULT']) && $row['EXTRA'] !== "auto_increment"){
                    $column['default_exist'] = true;
                    $column['auto_increment'] = false;
                }else if(!is_null($row['COLUMN_DEFAULT']) && $row['EXTRA'] === "auto_increment"){
                    $column['default_exist'] = true;
                    $column['auto_increment'] = true;
                }else{
                    $column['default_exist'] = true;
                    $column['auto_increment'] = true; 
                }

                $props[$name] = $column;
            }

            return $props;

        }catch(Exception $e){ 
            // TODO implement
            throw e;
        } 
    }
}


?>
