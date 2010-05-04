<?php

include_once "chain_record_define.php";

/* $obj has same named properties with group table columns. */
$obj = new Group();

$ret = $obj->find(array(
        "order" => "id",
        "limit" => 1
    )
);

/* $ret is almost array (ChainTube Object) */
/* $m is a Group Model class object.       */
$m = $ret[0];

/* change title,memo property by new value */
$m->title = "new updated title";
$m->memo  = "new updated memo";

/* update by title="real updated title value" */
/*           memo ="new updated memo"         */
$m->update(array(
        "title" => "real updated title value"
    )
);

?>
