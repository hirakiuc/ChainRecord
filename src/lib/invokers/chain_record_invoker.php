<?php

require_once "lib/invokers/method_invoker.php";
require_once "lib/object_sorter.php";

class ChainRecordInvoker extends MethodInvoker {

    public function invoke($name, $parameters){

        $groups = $this->make_group_by_pdo();

        $target = null;
        $myself = new ReflectionClass(get_class($this));
        if($myself->hasMethod($name)){ 
            $target = $this;
        }else{ 
            $target = null;
        }

        $result = array();
        foreach($groups as $group){
            $grp_result = array();

            foreach($group as $v){
                $ret = call_user_func_array(
                    array($v, $name), $parameters);

                /* for by model */
                if($ret instanceof ChainTube){ 
                    $grp_result = array_merge($grp_result, $ret->to_a());
                }else{
                    array_push($grp_result, $ret);
                } 
            } 

            $result = array_merge($result, $grp_result);
        } 

        if($name === "find" && isset($parameters[0]["order"])){
            $result = $this->sort_by_keys($result, $parameters[0]["order"]);
        }

        return $result;
    }


    private function make_group_by_pdo(){
        // TODO implement (for transaction management : future release)
        return array($this->list);
    }

    private function sort_by_keys($list, $orderby){
        $sorter = new ObjectSorter($orderby);
        $sorted_list = $sorter->sort($list);

        return $sorted_list;
    }
} 

?>
