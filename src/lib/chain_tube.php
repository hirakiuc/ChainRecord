<?php

require_once "errors.php";


class ChainTube implements ArrayAccess, Iterator, Countable {
    /** position for iterator interface */
    private $pos = 0;

    protected $list = null;

    //------------------------------------------------
    // ArrayAccess Interface 
    // http://jp.php.net/manual/ja/class.arrayaccess.php
    public function offsetSet($offset, $value){
        $this->list[$offset] = $value;
    }
    public function offsetGet($offset){
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    } 
    public function offsetExists($offset){
        return isset($this->list[$offset]);
    }
    public function offsetUnset($offset){
        unset($this->list[$offfset]);
    }
    //------------------------------------------------
    // Iterator Interface 
    // http://jp.php.net/manual/ja/class.iterator.php
    function rewind(){
        $this->pos = 0;
    }
    function current(){
        return $this->list[$this->pos];
    }
    function key(){
        return $this->pos;
    }
    function next(){
        ++$this->pos;
    }
    function valid(){
        return isset($this->list[$this->pos]);
    }
    //------------------------------------------------
    // Countable Interface 
    // http://jp.php.net/manual/ja/countable.count.php
    public function count(){
        return count($this->list);
    } 
    //------------------------------------------------


    function __construct($list = null){
        $this->pos = 0;

        if(is_null($list)){
            $this->list = array();
        }else{
            $this->list = $list;
        } 
    } 

    public function filter($callback){
        $ary = array();

        foreach($this->list as $v){
            if($callback($v)){
                array_push($ary, $v);
            }
        }
        $this->list = $ary;
    }

    public function apply($ary){
        foreach($this->list as $v){
            call_user_func_array(
                array($ary['obj'], $ary['method']), 
                $ary['params']
            );
        }
    }

    public function each($callback){
        foreach($this->list as $v){
            $calllback($v);
        }
    }

    public function first(){
        return $this->list[0];
    }

    public function last(){
        $no = count($this->list) -1;
        return $this->list[$no];
    }

    public function size(){
        return count($this->list);
    }

    public function is_empty(){
        return empty($this->list);
    }

    public function to_a(){
        return $this->list;
    }


    function __call($name, $parameters){ 

        if(empty($this->list)){
            return $this;
        }

        $v = $this->list[0];

        $obj = null;
        if(is_integer($v)){
            $obj = new IntegerInvoker($this->list);
        }else if(is_float($v)){
            $obj = new FloatInvoker($this->list);
        }else if(is_string($v)){
            $obj = new StringInvoker($this->list);
        }else if(is_bool($v)){
            $obj = new BooleanInvoker($this->list);
        }else if(is_array($v)){
            $obj = new ArrayInvoker($this->list);
        }else if(is_object($v)){
            $obj = new ObjectInvoker($this->list);
        }

        if(is_null($obj)){
            $msg = "Unsupported Chain ValueType";
            throw new NotSupportError($msg); 
        }

        $this->list = $obj->invoke($name, $parameters);
        return $this;
    } 
}


?>
