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

            // TODO grouping by target database.
            // TODO call query optimizer if object is subclass of ChainRecord.
            // TODO make $this->list contains one object and pass optimized cond arg. 

            foreach($this->list as $v){
                $ret = call_user_func_array(
                    array($this,$name), $parameters);
                array_push($results, $ret);
            } 

            // TODO check whether called method is "find" and args has "order" opt.
            // TODO call sorter if $ret has object which is subclass of ChainRecord.

            return $results;
        }else{
            $msg = "Undefined Method Called:";
            $msg.= "(class:".get_class($this).", method:".$name.")";
            throw new Exception($msg);
        }
    } 
} 

?>
