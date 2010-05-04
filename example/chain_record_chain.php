<?php

include_once "chain_record_define.php";

/* $obj has same named properties with group table columns. */
$obj = new Group();

/* get rows as group model objects in groups table. */
$ret = $obj->find(array(
        "cond" => array("title = ? or title = ?", $t1, $t2),
        "order" => "id desc",
        "limit" => 5
    )
)->filter(create_function('$m','
        if($m->title === "invalid model"){
            return false;
        }else{
            return true;
        }
    ')
)->each(create_function('$m','
        $m->memo = "checked";
    ')
)->save(); 

?>
