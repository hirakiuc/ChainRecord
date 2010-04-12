<?php

set_include_path(get_include_path().PATH_SEPARATOR.getcwd()."/../src");

require_once "chain_record.php";

class Group extends ChainRecord{
    protected $primary_key = array("id");
}

ChainRecord::init("../data/config.yaml");

?>
