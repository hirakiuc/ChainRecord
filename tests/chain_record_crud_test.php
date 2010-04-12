<?php

require_once "PHPUnit/Framework.php";
require_once "test_helper.php";

class ChainRecordCRUDTest extends PHPUnit_Framework_TestCase {
    
    protected function setUp(){
        $grp = new Group();

        $grp->destroy();

        for($i = 0;$i < 10;$i++){
            $grp->create(array(
                    "title" => "title".$i,
                    "memo"  => "memomemo"
                )
            );
            $grp->save();
        } 
    }

    public function testFind_all(){
        $obj = new Group();

        $grps = $obj->find(); 
        $this->assertEquals(count($grps), 10);
    }

    public function testFind_limit(){
        $obj = new Group();

        $grps = $obj->find(array(
                "limit" => 2
            )
        );
        $this->assertEquals(count($grps), 2);
    }

    public function testFind_limit_with_offset(){
        $obj = new Group();

        $grps = $obj->find(array(
                "limit" => array(2, 3),
                "order" => "id"
            )
        );

        $i=3;
        foreach($grps as $m){
            $this->assertEquals("title".$i,$m->title);
            $i += 1;
        }
        
    }

    public function testFind_orderby_asc(){
        $obj = new Group();

        $grps = $obj->find(array(
                "order" => "id"
            )
        ); 
        for($i=1;$i<10;$i++){
            $prev = $grps[$i-1];
            $aftr = $grps[$i];

            $this->assertTrue( ($prev->id < $aftr->id) ); 
        } 
    }

    public function testFind_orderby_asc2(){
        $obj = new Group();

        $grps = $obj->find(array(
                "order" => "id asc"
            )
        ); 
        for($i=1;$i<10;$i++){
            $prev = $grps[$i-1];
            $aftr = $grps[$i];

            $this->assertTrue( ($prev->id < $aftr->id) ); 
        } 
    }

    public function testFind_orderby_desc(){
        $obj = new Group();

        $grps = $obj->find(array(
                "order" => "id desc"
            )
        ); 
        for($i=1;$i<10;$i++){
            $prev = $grps[$i-1];
            $aftr = $grps[$i];

            $this->assertTrue( ($prev->id > $aftr->id) ); 
        } 
    }

    public function testFind_all_property(){
        $obj = new Group();

        $grps = $obj->find();
        $v = $grps[0];

        $i = 0;
        foreach($grps as $m){ 
            $this->assertNotNull($m->id);
            $this->assertEquals("title".$i, $m->title);
            $this->assertEquals("memomemo", $m->memo);

            $i+=1;
        }
    }

    protected function tearDown(){
        $grp = new Group();

        $grp->destroy();
    }

}





?>
