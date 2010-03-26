<?php

require_once "errors.php";

class MethodInvoker{

  protected $list = null;

  function __construct($ary){
    $this->list = $ary;
  }

  public function invoke($name, $paramters){
    $myself = new ReflectionClass(get_class($this));
    if($myself->hasMethod($name)){ 
      $results = array();

      foreach($this->list as $v){
        $ret = call_user_func_array(
                array($this,$name), $parameters);
        array_push($results, $ret);
      } 
    }else{
      $msg = "Undefined Method Called:";
      $msg.= "(class:".get_class($this).", method:".$name.")";
      throw new CRException($msg);
    }
  }

}


?>
