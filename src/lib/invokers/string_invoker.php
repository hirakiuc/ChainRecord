<?php

class StringInvoker extends MethodInvoker {

  public function join($separator = ""){
    return implode($separator, $this->list);
  }

  public function unique(){
    // TODO not implemented
  }

  public function sort($callback){
    // TODO not implemented
  }

  public function trim(){
    // TODO not implemented
  }
  
  public function split($sep){
    // TODO not implemented
  }

  public function to_a(){
    return $this->list;
  } 
}

?>
