<?php

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
                if($target === null){
                    $target = $v;
                }
                $ret = call_user_func_array(
                    array($target, $name), $parameters);
                array_push($grp_result, $ret);
            } 

            array_push($result, $grp_result);
//            if(isset($parameters[0]["order"])){
//                $this->sort_by_keys($result, $keys, $order);
//            }
        } 

        return $result;
    }

    private function make_group_by_pdo(){
        // TODO group list by pdo
        return array($this->list);
    }

    private function sort_by_keys($list, $keys, $order){
        // TODO sort list by keys with order
        return $list;
    }
} 

?>
