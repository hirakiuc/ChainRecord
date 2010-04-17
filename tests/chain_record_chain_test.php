<?php

require_once "PHPUnit/Framework.php";
require_once "test_helper.php";

class TitleChanger{
    public function change($m){
        $m->title = "title-unknown"; 
    }
}

class ChainRecordChainTest extends PHPUnit_Framework_TestCase {
    protected function setUp(){
        $grp = new Group();

        $grp->destroy();

        for($i=0;$i<10;$i++){
            $grp->create(array(
                    "title" => "title".$i,
                    "memo"  => "memomemo"
                )
            );
            $grp->save();
        }

    }

    public function testFind_filter(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            ) 
        )->filter(create_function('$v','
                if($v->title === "title8"){
                    return false;
                }else{
                    return true;
                }
            ')
        );

        foreach($ret as $m){
            $this->assertNotEquals("title8", $m->title);
        }
    }

    public function testFind_apply(){
        $grp = new Group();
        $obj = new TitleChanger();

        $ret = $grp->find(array(
                "order" => "id"
            ) 
        )->apply(array(
                "obj" => $obj,
                "method" => "change"
            )
        );

        foreach($ret as $m){
            $this->assertEquals("title-unknown", $m->title);
        }
    }

    public function testFind_each(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            )
        )->each(create_function('$v','
                $v->title = "hogehoge";
            ')
        );

        foreach($ret as $v){
            $this->assertEquals("hogehoge", $v->title);
        }
    }

    public function testFind_first(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            )
        )->first();

        $this->assertEquals("title0", $ret->title);
    }

    public function testFind_last(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            )
        )->last();

        $this->assertEquals("title9", $ret->title);
    }

    public function testFind_size(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            )
        )->size();

        $this->assertEquals(10, $ret);
    }

    public function testFind_is_empty_false(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            )
        )->is_empty();

        $this->assertFalse($ret);
    }

    public function testFind_is_empty_true(){
        $grp = new Group();

        $ret = $grp->find(array(
                "cond" => array("title = 'z'"),
                "order" => "id"
            )
        )->is_empty();

        $this->assertTrue($ret);
    }

    public function testFind_to_a(){
        $grp = new Group();

        $ret = $grp->find(array(
                "order" => "id"
            )
        )->to_a();

        $this->assertTrue(is_array($ret));
    }

    protected function tearDown(){
        $grp = new Group();
        $grp->destroy();
    }
}



?>
