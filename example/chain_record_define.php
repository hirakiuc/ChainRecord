<?php

set_include_path(get_include_path().PATH_SEPARATOR.getcwd()."/../src");

require_once "chain_record.php";

/* Initialize ChainRecord Library */
ChainRecord::init("../data/config.yaml");

/*
 * -- groups table DDL
 *
 * CREATE TABLE groups(
 *   id INTEGER NOT NULL AUTO_INCREMENT,
 *   title TEXT,
 *   memo TEXT,
 *   PRIMARY KEY (id)
 * );
 */

/* define Model Class extends ChainRecord Class. */
class Group extends ChainRecord{
    protected $primary_key = array("id");
} 

?>
