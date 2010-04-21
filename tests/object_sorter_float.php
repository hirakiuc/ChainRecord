<?php

require_once "PHPUnit/Framework.php";
require_once "test_helper.php";
require_once "../src/lib/object_sorter.php";

class Sample{
    public $prop1 = null;
    public $prop2 = null;
    public $prop3 = null;

    public function __construct($i=0,$j=0,$k=0){
        $this->prop1 = $i;
        $this->prop2 = $j;
        $this->prop3 = $k;
    }

    public function to_string(){
        return $this->prop1." - ".$this->prop2." - ".$this->prop3."\n";
    } 
}

class ObjectSorterFloatTest extends PHPUnit_Framework_TestCase {
    
    protected $list = null;

    protected function setUp(){
        $this->list = array();

        for($i=0;$i<1000;$i++){
            array_push($this->list, 
                new Sample(rand(1000,10000)/100000, rand(100,1000)/100000)
            );
        }
    }

    public function dump($list){
        foreach($list as $m){
            echo $m->to_string();
        } 
    }

    public function testIntSort_with_one_key(){
        $sorter = new ObjectSorter("prop1");
        $ret = $sorter->sort($this->list); 

        for($i=0;$i<count($ret)-1;$i++){ 
            $this->assertTrue( bccomp($ret[$i]->prop1, $ret[$i+1]->prop1) <= 0);
        }
    }

    public function testIntSort_with_one_key_desc(){
        $sorter = new ObjectSorter("prop1 desc");
        $ret = $sorter->sort($this->list);

        for($i=0;$i<count($ret)-1;$i++){ 
            $this->assertTrue( bccomp($ret[$i]->prop1, $ret[$i+1]->prop1) >= 0 );
        } 
    }

    public function testIntSort_with_two_key(){
        $sorter = new ObjectSorter("prop1,prop2");
        for($i=0;$i<count($this->list);$i++){
            $this->list[$i]->prop1 = bcmod($i,10);
        }

        $ret = $sorter->sort($this->list);

        for($i=0;$i<count($ret)-1;$i++){ 
            $this->assertTrue( bccomp($ret[$i]->prop1, $ret[$i+1]->prop1) <= 0);
            if($ret[$i]->prop1 === $ret[$i]->prop2){
                $this->assertTrue( bccomp($ret[$i]->prop2, $ret[$i+1]->prop2) <= 0);
            }
        }
    }

    public function testIntSort_with_two_key_desc(){
        $sorter = new ObjectSorter("prop1,prop2 desc");
        for($i=0;$i<count($this->list);$i++){
            $this->list[$i]->prop1 = bcmod($i,10);
        }

        $ret = $sorter->sort($this->list);

        for($i=0;$i<count($ret)-1;$i++){ 
            $this->assertTrue( bccomp($ret[$i]->prop1, $ret[$i+1]->prop1) >= 0 );
            if($ret[$i]->prop1 === $ret[$i]->prop2){
                $this->assertTrue( bccomp($ret[$i]->prop2, $ret[$i+1]->prop2) >= 0 );
            }
        }
    }

    protected function tearDown(){

    }
}
