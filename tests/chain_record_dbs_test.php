<?php

require_once "PHPUnit/Framework.php";
require_once "test_helper.php";

class ChainRecordDbsTest extends PHPUnit_Framework_TestCase {
    
    protected function setUp(){
        $grp = new Group();

        $grp->destroy(array(
                "dbs" => array("testdb1", "testdb2")
            )
        );

        for($i = 0;$i < 10;$i++){
            $grp->create(array(
                    "title" => "title".$i,
                    "memo"  => "memomemo"
                )
            );
            $grp->save(array(
                    "dbs" => array("testdb1", "testdb2")
                )
            );
        } 
    }

    public function testFind_all(){
        $obj = new Group();

        $grps = $obj->find(array(
                "dbs" => array("testdb2", "testdb1")
            )
        ); 
        $this->assertTrue($grps instanceof ChainTube);
        foreach($grps as $m){
            $this->assertTrue(is_subclass_of($m, "ChainRecord"));
        }

        $this->assertEquals(20, count($grps)); 
    }

    public function testFind_all_property(){
        $obj = new Group();

        $grps = $obj->find(array(
                "dbs" => array("testdb2")
            )
        );

        $i = 0;
        foreach($grps as $m){ 
            $this->assertNotNull($m->id);
            $this->assertEquals("title".$i, $m->title);
            $this->assertEquals("memomemo", $m->memo);

            $i+=1;
        }
    }

    public function testFind_limit(){
        $obj = new Group();

        $grps = $obj->find(array(
                "limit" => 2,
                "dbs"   => array("testdb2")
            )
        );
        $this->assertEquals(count($grps), 2);
    }

    public function testFind_limit_with_offset(){
        $obj = new Group();

        $grps = $obj->find(array(
                "limit" => array(2, 3),
                "order" => "id",
                "dbs"   => array("testdb2")
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
                "order" => "id",
                "dbs"   => array("testdb1", "testdb2")
            )
        ); 
        for($i=1;$i<20;$i++){
            $prev = $grps[$i-1];
            $aftr = $grps[$i];

            $this->assertTrue( ($prev->id <= $aftr->id) ); 
        } 
    }

    public function testFind_orderby_asc2(){
        $obj = new Group();

        $grps = $obj->find(array(
                "order" => "id asc",
                "dbs" => array("testdb1","testdb2")
            )
        ); 
        for($i=1;$i<20;$i++){
            $prev = $grps[$i-1];
            $aftr = $grps[$i];

            $this->assertTrue( ($prev->id <= $aftr->id) ); 
        } 
    }

    public function testFind_orderby_desc(){
        $obj = new Group();

        $grps = $obj->find(array(
                "order" => "id desc",
                "dbs"   => array("testdb1", "testdb2")
            )
        ); 
        for($i=1;$i<20;$i++){
            $prev = $grps[$i-1];
            $aftr = $grps[$i];

            $this->assertTrue( ($prev->id >= $aftr->id) ); 
        } 
    }

    public function testFind_cond(){
        $obj = new Group();

        $grps = $obj->find(array(
                "cond" => array("title = 'title2'"),
                "dbs"  => array("testdb1", "testdb2")
            )
        );

        $this->assertEquals(2, count($grps));
        $this->assertEquals("title2", $grps[0]->title);
        $this->assertEquals("title2", $grps[1]->title);
    }

    public function testFind_cond_with_param(){
        $obj = new Group();
        $p1 = "title2";

        $grps = $obj->find(array(
                "cond" => array("title = ?", $p1),
                "dbs"  => array("testdb1", "testdb2")
            )
        );

        $this->assertEquals(2, count($grps));
        $this->assertEquals("title2", $grps[0]->title);
        $this->assertEquals("title2", $grps[1]->title); 
    }

    public function testFind_cond_with_params(){
        $obj = new Group();
        $p1 = "title2";
        $p2 = "memomemo";

        $grps = $obj->find(array(
                "cond" => array("title = ? AND memo = ?", $p1, $p2),
                "dbs"  => array("testdb1", "testdb2")
            )
        );

        $this->assertEquals(2, count($grps));
        $this->assertEquals("title2", $grps[0]->title);
        $this->assertEquals("memomemo", $grps[0]->memo);
        $this->assertEquals("title2", $grps[1]->title);
        $this->assertEquals("memomemo", $grps[1]->memo);
   }

    public function testFind_with_pkey(){
        $obj = new Group();

        $grps = $obj->find(array(
                "limit" => 1,
                "dbs"   => array("testdb2")
            )
        );

        $this->assertEquals(1, count($grps));
        $grp = $grps[0];
        $before_id = $grp->id;

        $this->assertTrue( is_subclass_of($grp,"ChainRecord") );
        $after_grps = $grp->find(array(
                "dbs" => array("testdb2")
            )
        );
        $this->assertEquals(1, count($after_grps));
        $this->assertEquals($before_id, $after_grps[0]->id); 

        $obj2 = new Group();
        $obj2->id = $before_id;

        $after2_grps = $obj2->find(array(
                "dbs" => array("testdb2")
            )
        );
        $this->assertEquals(1, count($after2_grps));
        $this->assertEquals($before_id, $after2_grps[0]->id);
    }

    public function testCreate_and_Save(){
        $obj = new Group();
        $title = "hogehuga";
        $memo  = "hogehuga";

        $obj->create(array(
                "title" => $title,
                "memo"  => $memo
            )
        );
        $ret = $obj->find(array(
                "cond" => array("title = ? AND memo = ?", $title, $memo)
            )
        );
        $this->assertEquals(0, count($ret));

        $obj->save();
        $ret = $obj->find(array(
                "cond" => array("title = ? AND memo = ?", $title, $memo)
            )
        );
        $this->assertEquals(1, count($ret)); 
    }

    public function testFindUpdate(){
        $obj = new Group();

        $ret = $obj->find(array(
                "cond" => array("title = 'title8'")
            )
        );
        $this->assertEquals(1, count($ret));
        $this->assertEquals("title8", $ret[0]->title);

        $new_title = "new title";
        $ret[0]->title = $new_title;
        $ret2 = $ret[0]->update();

        $this->assertEquals(1, $ret2);

        $ret2 = $ret[0]->find();
        $this->assertEquals(1, count($ret2));
        $this->assertEquals($new_title, $ret2[0]->title); 
    }

    public function testFindUpdate_by_argument(){
        $obj = new Group();

        $ret = $obj->find(array(
                "cond" => array("title = 'title8'")
            )
        );
        $this->assertEquals(1, count($ret));
        $this->assertEquals("title8", $ret[0]->title);

        $new_title = "new title";
        $ret[0]->title = "old_title_hogehoge";
        $ret2 = $ret[0]->update(array(
                "vals" => array(
                    "title" => $new_title
                )
            )
        ); 
        $this->assertEquals(1, $ret2);

        $ret2 = $ret[0]->find();
        $this->assertEquals(1, count($ret2));
        $this->assertEquals($new_title, $ret2[0]->title); 
    }


    public function testSaveUpdate(){
        $obj = new Group();
        $title = "たいとる";
        $memo = "めもめも";

        $m = $obj->create(array(
                "title" => $title,
                "memo" => $memo
            )
        );
        $ret = $m->save();
        $this->assertTrue($ret);

        $ret = $obj->find(array(
                "cond" => array("title = ? AND memo = ?", $title, $memo)
            )
        );
        $this->assertEquals(1, count($ret));
        $this->assertEquals($title, $ret[0]->title);
        $this->assertEquals($memo,  $ret[0]->memo);

        $m = $ret[0];
        $new_title = "new title";
        $new_memo  = "new memo";

        $m->title = $new_title;
        $m->memo = $new_memo;

        $ret = $m->update();
        $this->assertEquals(1, $ret);

        $ret = $obj->find(array(
                "cond" => array(
                    "title = ? AND memo = ?", 
                    $new_title, $new_memo
                )
            )
        );
        $this->assertEquals(1, count($ret));
        $this->assertEquals($new_title, $ret[0]->title);
        $this->assertEquals($new_memo , $ret[0]->memo);
    }

    public function testDestroy(){
        $obj = new Group();

        $ret = $obj->find();
        $this->assertEquals(10, count($ret));

        $ret = $obj->destroy(array(
                "cond" => array("title = 'title2'")
            )
        );
        $this->assertEquals(1, $ret);

        $ret = $obj->find(array(
                "cond" => array("title = 'title2'")
            )
        );
        $this->assertEquals(0, count($ret));
    }

    public function testDestroy_with_param(){
        $obj = new Group();

        $ret = $obj->find();
        $this->assertEquals(10, count($ret));

        $title = "title2";
        $ret = $obj->destroy(array(
                "cond" => array("title = ?", $title)
            )
        );
        $this->assertEquals(1, $ret);

        $ret = $obj->find(array(
                "cond" => array("title = ?", $title)
            )
        );
        $this->assertEquals(0, count($ret));
    }

    public function testDestroy_with_pkey(){
        $obj = new Group();

        $ret = $obj->find();
        $this->assertEquals(10, count($ret));

        $target = $ret[5];
        $ret = $target->destroy();
        $this->assertEquals(1, $ret);

        $ret = $obj->find(array(
                "cond" => array("title = ?", $target->title)
            )
        );
        $this->assertEquals(0, count($ret));
    }

    public function testDestroy_with_pkey2(){
        $obj = new Group();

        $ret = $obj->find();
        $this->assertEquals(10, count($ret));

        $target = $ret[5];
        $obj2 = $obj->create(array(
                "id" => $target->id
            )
        );
        $ret = $obj2->destroy();
        $this->assertEquals(1, $ret);

        $ret = $obj->find(array(
                "cond" => array("title = ?", $target->title)
            )
        );
        $this->assertEquals(0, count($ret));
    }


    protected function tearDown(){
        $grp = new Group(); 
        $grp->destroy(array(
                "dbs" => array("testdb1", "testdb2")
            )
        );
    } 
} 

?>
