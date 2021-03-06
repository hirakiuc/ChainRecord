<?php
require_once "lib/meta_driver_factory.php";
require_once "lib/errors.php";

class PgSQLMetaDriver extends MetaDriver{ 
    public function getColumns($pdo, $table_name){
        $query = "SELECT ";
        $query.= "column_name, data_type, column_default,";
        $query.= "character_maximum_length, is_nullable, ordinal_position ";
        $query.= "FROM information_schema.columns ";
        $query.= "WHERE table_name = ? ";
        $query.= "ORDER BY ordinal_position";

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

                $name = $row['column_name'];
                $column['type'] = $row['data_type'];
                $column['length'] = $row['character_maximum_length'];
                $column['nullable'] = $row['is_nullable'];

                if(is_null($row['column_default'])){
                    $column['default_exist'] = false;
                    $column['auto_increment'] = false;
                }else{
                    if(preg_match("/^nextval/",$row['column_default']) === 1){
                        $column['default_exist'] = true;
                        $column['auto_increment'] = true;
                    }else{
                        $column['default_exist'] = true;
                        $column['auto_increment'] = false;
                    }
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
