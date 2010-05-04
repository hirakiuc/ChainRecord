<?php

include_once "chain_record_define.php";

/* $obj has same named properties with group table columns. */
$obj = new Group();

$t1 = "destroy target record title";

/* delete groups table records */
$obj->destroy(array(
        "cond" => array("title = ?",$t1)
    )
);
/* SQL: DELETE FROM groups WHERE title = ?   */
/* parameter $t1 bind to '?' by database api */

?>
