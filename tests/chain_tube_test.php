<?php

require_once "PHPUnit/Framework.php";
require_once "test_helper.php";

class ChainTubeTest extends PHPUnit_Framework_TestCase{

    public function testForeach(){
        $list = array(1,2,3,4,5,6);
        $tube = new ChainTube($list);

        foreach($tube as $v){
            $this->assertTrue(is_numeric($v));
        }
    }
}

?>
