<?php

class ObjectSorter{

    public static $ASC = "asc";

    public static $DESC = "desc";

    private $sort_keys = null;

    private $order = null;

    public function __construct($sort_str){ 
        $this->list = array();
        $this->sort_keys = array();

        $ret = explode(",", $sort_str);
        foreach($ret as $key){
            $v = explode(" ", $key);
            if(count($v) === 2){
                array_push($this->sort_keys, trim($v[0]));
                $this->order = strtolower(trim($v[1]));
            }else if(count($v) === 1){ 
                array_push($this->sort_keys, trim($key));
            }else{
                throw new CRError("invalid parameter error");
            }
        }

        if(is_null($this->order)){
            $this->order = "asc";
        } 
    } 

    public function sort($list){ 
        $this->list = $list;

        $this->qsort(0, count($this->list)-1);

        return $this->list;
    }

    private function qsort($i, $j){
        if($i == $j){
            return;
        }

        $p = $this->pivot($i, $j);
        if($p !== -1){
            $k = $this->partition($i, $j, $this->list[$p]);
            $this->qsort($i, $k-1);
            $this->qsort($k, $j);
        }
    }

    private function pivot($i, $j){
        $k = $i + 1;
        while($k <= $j && $this->compare($this->list[$i], $this->list[$k]) === 0){
            $k+=1;
        }

        if($k > $j){
            return -1;
        }

        $ret = $this->compare($this->list[$i], $this->list[$k]);
        if($ret >= 0){
            return $i;
        }

        return $k;
    }

    private function partition($i, $j, $x){
        $left = $i;
        $right= $j;

        while($left <= $right){
            while($left <= $j && $this->compare($this->list[$left], $x) < 0){
                $left += 1;
            }

            while($right >= $i && $this->compare($this->list[$right], $x) >= 0){
                $right -= 1;
            }

            if($left > $right){
                break;
            }
            $this->swap($left, $right);
            $left  += 1;
            $right -= 1;
        }
        return $left;
    }

    private function swap($i, $j){
        $v = $this->list[$i];
        $this->list[$i] = $this->list[$j];
        $this->list[$j] = $v;
    }

    /**
     * if $this->order === "asc"
     *   return  1 if m1 > m2
     *   return  0 if m1 = m2
     *   return -1 if m1 < m2
     * else (if $this->order === "desc")
     *   return -1 if m1 > m2
     *   return  0 if m1 = m2
     *   return  1 if m1 < m2 
     */
    private function compare($m1, $m2){
        foreach($this->sort_keys as $key){
            $ret = $this->comp($m1, $m2, $key);
            if($ret !== 0){
                return $ret;
            }
        }
        return 0;
    }

    private function comp($m1, $m2, $key){
        $v1 = $m1->$key;
        $v2 = $m2->$key;

        $compared = 0;

        if(is_null($v1) && is_null($v2)){
            $compared = 0;
        }else if(is_null($v1) && !is_null($v2)){
            $compared = -1;
        }else if(!is_null($v1) && is_null($v2)){
            $compared = 1;
        }else{ 
            if(is_string($v1) && is_string($v2)){
                $compared = strcmp($v1, $v2);
            }else if(is_float($v1) && is_float($v2)){
                $v1_str = sprintf("%F",$v1);
                $v2_str = sprintf("%F",$v2);
                $compared = bccomp($v1_str, $v2_str);

            }else if(is_double($v1) && is_double($v2)){
                $v1_str = sprintf("%f", $v1);
                $v2_str = sprintf("%f", $v2);
                $compared = bccomp($v1_str, $v2_str);
            }else{
                if($v1 > $v2){
                    $compared = 1;
                }else if($v1 < $v2){
                    $compared = -1;
                }else{
                    $compared = 0;
                }
            }
        }

        if($compared === 1){
            return ($this->order === "asc") ?  1 : -1;
        }else if($compared === -1){
            return ($this->order === "asc") ? -1 :  1;
        }else{
            return 0;
        }
    }
} 

?>
