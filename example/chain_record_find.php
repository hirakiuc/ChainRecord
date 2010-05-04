<?php

include_once "chain_record_define.php";

/* $obj has same named properties with group table columns. */
$obj = new Group();

$t1 = "this script writen by php.";
$t2 = "how to use ChainRecord library.";

/* get rows as group model objects in groups table. */
$ret = $obj->find(array(
        "cond" => array("title = ? or title = ?", $t1, $t2),
        "order" => "id desc",
        "limit" => 5
    )
); 

?>
