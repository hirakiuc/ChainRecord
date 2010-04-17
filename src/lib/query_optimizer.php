<?php


class QueryOptimizer{

    protected $list = null;

    public function __construct($list){
        $this->list = $list;
    }

    public get_query_condition($cond, $params){

        if(count($this->list) == 0){
            return null;
        }

        $is_pkey_set = ReflectionMethod(
            get_class($this->list[0]), "is_pkey_set"
        );
        $get_pkey_cond = ReflectionMethod(
            get_class($this->list[0]), "get_pkey_cond"
        );

        $cond_list = array(); 

        foreach($this->list as $m){
            if($is_pkey_set($m)){
                $v = $m->get_pkey_cond(); 

                array_push($cond_list, $v["cond"]);
                $params = array_merge($params, $v["params"]);
            }else{
                return null;
            } 
        }

        return array_merge(
            $cond." ".implode($cond_list, " OR "),
            $params
        );
    }
}


?>
