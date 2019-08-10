<?php
class validate {
    public function datatype($type,$data){
        if ($type=='1'){//Assiging a key
            if(ctype_alpha($data)){//Checking if the data is alpha-numeric
                $tt='';//The result if it is true
            }
        }
        if ($type == '2'){
            if(ctype_alnum($data)){
                $tt = '';
            }
        }
        
        if ($type =='3'){
            if(ctype_digit($data)){
                $tt='';
            }
        }
        if ($type =='4'){
                if(preg_match(" /[A-Z]/",$data) && preg_match(" /[0-9]/",$data) && preg_match(" /[!@]/",$data)){
                $tt='';
                }
           }
       
       if ($type == '5'){
           if(!filter_var($data, FILTER_VALIDATE_EMAIL) === FALSE){
               $tt = '';
           }
       }
       if ($type =='6'){
        if(strlen($data)>=8){
            $tt='';
        }
    }
    if ($type =='7'){
        if(preg_match(" /[A-Z]/",$data) || preg_match(" /[0-9]/",$data) || preg_match(" /[-., ]/",$data) || preg_match(" /[a-z]/",$data)) {
        $tt='';
        }

    if ($type =='8'){
            if(strlen($data)==11){
                $tt='';
            }
    }
    if ($type =='9'){
        if(ctype_digit($data)){
            $tt='';
        }
    }

    if (isset($tt)){
        return 1;
    }else{
        return 0;
     }
    }
}
}
class verify{
function randomize(){
    $val='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random= str_shuffle($val);
    $randomselect=substr($random,0,12);
    return $randomselect;
}
}

         

//figure out how to use Mysqli, How to connect the app layer to the persistem
// How to connect 

    


?>