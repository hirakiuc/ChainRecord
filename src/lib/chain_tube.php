<?php

require_once "errors.php";

// TODO implement array interface (ArrayObject?)
class ChainTube {

  protected $list = null;

  function __construct($list = null){
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
        array($ary['obj'], $ary['method']), $ary['params']
      )
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

  public function empty(){
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
    if(is_integer($v){
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
      if(is_subclass_of($v, "ChainRecord"){
        $obj = new ChainRecordInvoker($this->list);
      }
    }

    if(is_null($obj)){
      $msg = "Unsupported Chain ValueType";
      throw new CRException($msg);
    }

    $this->list = $obj->invoke($name, $parameters);
    return $this;
  }

}


?>
