<?php

class IntegerInvoker extends MethodInvoker {

    public function sum(){
        $sum = 0;
        foreach($this->list as $v){
            $sum+= $v;
        }
        return $sum;
    }

    publc function max(){
        $max = null;
        foreach($this->list as $v){
            if(is_null($max)){
                $max = $v;
            }

            if($v > $max){
                $max = $v;
            }
        }

        return $max;
    }

    public function min(){
        $min = null;
        foreach($this->list as $v){
            if(is_null($min)){
                $min = $v;
            }

            if($v < $min){
                $min = $v;
            }
        }
        return $min;
    }

    public function avg(){
        if(empty($this->list)){
            return false;
        }

        $sum = 0;
        foreach($this->list as $v){
            $sum += $v;
        }

        return $sum/count($this->list);
    } 
} 

?>
