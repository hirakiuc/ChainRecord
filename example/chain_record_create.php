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

/* $title = "ChainRecord Introduction." */
$title = $m->title;

/* $memo = "This is sample code." */
$memo  = $m->memo;

/* you can set property. */
$m->title = "ChainRecord involved.";
/* now $m->title property set. */ 

?>
