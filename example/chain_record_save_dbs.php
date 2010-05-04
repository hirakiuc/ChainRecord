<?php

include_once "chain_record_define.php";

/* $obj has same named properties with group table columns. */
$obj = new Group();

$obj->destroy(array(
        "dbs" => array("testdb1","testdb2")
    )
); 

/* Create model object with hash array. */
$m = $obj->create(array(
        "title" => "ChainRecord Introduction.",
        "memo"  => "This is sample code."
    )
);

/* save model object (insert) */
/*
try{
    $m->save(array(
            "dbs" => array("testdb1","testdb2")
        )
    );
}catch(Exception $e){
  echo "save failed.\n";
}
 */
?>
