<?php

require_once "PHPUnit/Framework.php";

class AllTests{
    public static function suite(){

        $suite = new PHPUnit_Framework_TestSuite("ChainRecord");

        $suite->addTestFile("chain_record_crud_test.php");
        $suite->addTestFile("chain_record_chain_test.php");
        $suite->addTestFile("chain_record_dbs.test.php");
        $suite->addTestFile("chain_tube_test.php");

        $suite->addTestFile("object_sorter_int_test.php");
        $suite->addTestFile("object_sorter_float.php");
        $suite->addTestFile("object_sorter_str_test.php");


        return $suite; 
    } 
} 

?>
