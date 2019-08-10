<?php
class dbcon{//creating class that our connecting will be under
   public function conn($cdb){ // method that holds the connect
    if($cdb == ''){$connectto = 'schoolproject';}else{$connectto = $cdb;}
    $dinma = new mysqli('localhost', 'root', '', "$connectto");//this were you instanstiate the call called connection "mysqli"z
    if($dinma == FALSE) {// if the connection doesn't hold this connection will not run
        die('ERROR: could not connect');
    }else{return $dinma;}
   
    
   }
}

//$this->connect=$this->conn(); the methods that is inside conn is now inside
class dbops extends dbcon {//we extend the first class to the second one so everything in the first one will be in the second one
    
    function __construct($validate2 = ''){//this runs automatically when this class is called   the sql is the data we are expecting, ($this ->=2 is an object) Reada bout the query method, fetch array, fetch object,num rows, affected-rows.
    $this->connect= $this->conn($validate2);// we are calling class inside a class making it an object. they are 2 because we need to be using it in other places
    }
    // pushquery doesn't generate error what makes pushquery to have error is what comes inside of it
    public function pushquery($data){//this method help us to pushing the queries(transactions) to the database
   $this-> data=$data;
   $this->result =$this->connect->query($this->data);//query is in the connect, and we use the query to connect mysql with php. Query is 
   if($this->result){ return $this->result;
   }else{die( $this->connect->error." PROBLEM WITH ".$this->data);}//The error is inbuilt and Error is a method that handles or picks out any error that happens.  
}


    public function buildselect($table,$col,$where,$limit,$fetchornot){ 
        $this->select = "select $col FROM $table WHERE $where $limit"; // this-select is used to building the select query in the database
       if($this->pushquery($this->select)){//the pushquery is a method that helps push query to the database.                                                     
        $numrows=$this->result->num_rows;//the num_rows help you to see the number of rows. The result is from the pushquery and it contains the No. of rows and the content of the pushquery

        if($fetchornot == 1){
            $res = array();
            while($kal = $this->result->fetch_assoc()){
                $res[ ]=$kal;
                return array('ugozee99'=>$res, 'numrows'=>$numrows);}
        }
        else {return $numrows;}    
        }
       else {return false;} 
    }                                                            
   
   public function buildinsert($table, $col, $values){
    $this->insert = "INSERT INTO $table($col)VALUES($values)";
if($this->pushquery($this->insert)){
    return TRUE;
}else {
    return FALSE;
}           
}
public function createdatabase($database){
    $this->create = "create database $database";
    if($this->pushquery($this->create)){
        return true;
    }else {return false;}

}
public function createtable(){
    $this->createtable = "create table Reg_table(sn int(6) AUTO_INCREMENT PRIMARY KEY,username varchar(30) not null,password varchar(30) not null,reg_date TIMESTAMP)";
   if ($this->pushquery($this->createtable)){
       return true;
   }else {return false;}      
}
public function updatedb($values,$email){
    $this->update = "UPDATE amahjosh SET $values where email='$email'";
    if($this->pushquery($this->update)){
        return true;
    }else {return false;}
}

 
}                                                                    
?>