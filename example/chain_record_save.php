<?php

include_once "chain_record_define.php";

/* $obj has same named properties with group table columns. */
$obj = new Group();

/* Create model object with hash array. */
$m = $obj->create(array(
        "title" => "ChainRecord Introduction.",
        "memo"  => "This is sample code."
    )
);

/* save model object (insert) */
try{
  $m->save();
}catch(Exception $e){
  echo "save failed.\n";
}

?>
