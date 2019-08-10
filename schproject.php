<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers: X-Requested-With,Authorrization, content-type, access-control-allow-origin,access-control-allow-methods, access-control-allow-headers");
$project=json_decode($data = file_get_contents('php://input'), TRUE);//Decode the payload that was sent during the API call that was made
include 'class/generalop.php';// This is where we copy the codes in the generalop file into schproject file.
include 'lib/dblib.php';
require ('class/jwtimplement.php');
$forjwt=new HandleJwt;
$validate= new validate;// This is where we represent the class name validate to $validate
$validate2= new dbops('');
$validateverify= new verify;


//A346 is login, A347 is sigin in, A348 is Merchant Sign up
$keys= array ('A347', 'A346','A348','AB1','A400');//To put the acceptable keys there, So when people call the API, We take the key coming from the call and check it inside the array.
//declare keys that will require jwt check
$jwtkeys = array('AB1');
if(isset($project['key'])){//We are checking if key is part of the payload.Is key part of the call that came.

    if(in_array($project['key'],$keys)){ // checking if key that was sent as the payload is that of the acceptable key that was sent or if there is oppration is set
   // check if key is a jwt operation key 
        if(in_array($project['key'],$jwtkeys)){
       // $goahead=TRUE; //We check if  
       //decode key by calling decode method
       $decodejwt = $forjwt::openTokenfull($project['load']);
       //check if decode worked by checking if result is an array
       if(is_array($decodejwt)){
           //check valid for expiry
           $checkvslid = $forjwt::checkifcookieisvalid($decodejwt);
           if($checkvslid == TRUE){
               extract($decodejwt);
               $jwtuser = $id;
               $goahead=TRUE;
           }else {
               // return r code to be 0 with error message to redirect to login at the front
           }
       }else{
           // return r code to be 0 with error message to redirect to login at the front
       }
       //var_dump($checkvslid); die();


    }else {$goahead=TRUE;}

    }else {$error = "INVALID ACTIVITY";}
}else {$error='KEY REQUIRED';}


   //die($error);
  if (isset($goahead)){

    //SIGNUP BLOCK
    if ($project['key']=='A347'){
        $reqfields=array('surname','name','email','password');//To store data in array.//This help us to check if the payload is contained in the array
        //$allfield=array()
        $allfields=array('surname','name','email','password','othername');
        $recval=array();
        foreach ($project as $vv =>$value){//To split the payload to bring out those keys
         array_push($recval,$vv);//Push the splited keys from the project into the recval  
        }
        
        foreach ($reqfields as $ff){//for each is use for data that is not associative. To check the required fields if all recval fields are available in the required fields
            if(in_array($ff,$recval)){
                $goahead=TRUE;
            }else { 
                 
                $error=TRUE; 
                $rcode = '0';
                $message = "$ff.' '.'required'";
                break;
            }
        }
    
    if(!isset($error)){//This line checks if there is no error then the next code will run.
        
        foreach($allfields as $mm){// We looping through $allfields array assigning it to $mm
            if (($mm=='surname' || $mm=='name')|| ($mm=='othername' && $project['othername'] !=' ')){//We are checking if surname,name,othername inside $mm meet the requirement
                $vv=$validate->datatype ('2', $project[$mm]);//We are trying to check if all fields i.e surname,name,email and password is in correct format which is alpha-num 
                if ($vv=='0'){
                    $error = ""; 
                    $rcode = '0';
                    $message =  "$mm.' '.'has to be available'"; 
                    break;
                }//if the $vv returns false which is zero it should indicate that the any of the looped missing variable "has to be available" and stop the running of the code  
            }

            if($mm == 'email'){//We are trying to check if $mm which is email meet the requirement 
                $vv=$validate->datatype('5', $project[$mm]);//We are trying to check if email is in the correct format 
                if ($vv=='0'){ 
                    $error = "";
                    $rcode = '0';
                    $message = "$mm.' '.'has to be an email'"; 
                    break;}//if the $vv returns false which is zero it should indicate that the any of the looped missing variable "has to be available" and the stop the running of the code
            }
            if($mm == 'password'){//We are trying to check if $mm which is password meet the requirement
                $vv=$validate->datatype('4', $project[$mm]);//We are trying to check if the field i.e password is in correct format 
                if ($vv=='0'){
                    $error = "";
                    $rcode = '0'; 
                    $message = "$mm.' '.'not strong'";}//if the $vv returns false which is zero it should indicate that the any of the looped missing variable "not in the right format" and the stop the running of the code
           }
            if($mm == 'password'){
             $vv=$validate->datatype('6', $project[$mm]);  
                if ($vv=='0'){
                $error = "";
                $rcode = '0'; 
                $message = "$mm.' '.'not in the right format'";
                 }
            }
    }

      //We check if there is no error
        if(!isset($error)){ 
           // extract email from the payload to check if email exists
           $values = $project['email']; 
           //Define table for the check
           $table="amahjosh";
//Define where clause for the check
           $where= "email='$values'";
           //define columns for check,, use only email here to save ram
           $col="email";
           $fetchornot = " ";
           // check if email exists.. send  table col and where to the sql library and call the select method
           $checkemail=$validate2->buildselect($table,$col,$where,'','');
         //  echo $checkemail; die();
         //if email does not exist
         
           if($checkemail == 0){
               // since we are running insert on the same table as defined before we will still use the table defined previously
           // define the inser column
        //    $colinsert = "SURNAME,NAME,OTHERNAME,EMAIL,PASSWORD";
        //    // DEFINE AND CONVERT ALL THE VALUES TO VARIABLES BEFORE USING THEM HERE or
        //    //extract($project);
        //    $surname= $project['surname'];
        //    $name = $project['name'];
        //    $othernames = $project['othername'];
        //   $email = $project['email'];
        extract($project);
          $password = SHA1(md5($project['password']));
                       //Define the values
           $colinsert = "SURNAME,NAME,OTHERNAME,EMAIL,PASSWORD";
           $valinsert = "'$surname','$name','$othername','$email','$password'";
           // call the insert method from the library
            $insertdata=$validate2->buildinsert($table, $colinsert, $valinsert);
            $random=$validateverify->randomize();
            $t=time();
            $column=' Email, Random_number, Time, Status';
            $values= "'$email', '$random','$t','0' ";
            $table= 'verification_table';
            $insertdata=$validate2->buildinsert($table, $column, $values);

//var_dump($insertdata); die();
         $encryptemail=sha1($email);
         $newemail= substr($encryptemail,0,10);
         //$createdatabase = $validate2->createdatabase($newemail);
         

         $issuer='http://localhost:4200';
        $audience='http://localhost:4200/#/';
        $user_id=$project['email'];
        $unique_id= sha1($user_id);
        $t=time() + 900;
        $jay=$forjwt::encryptjwt($issuer,$audience,$user_id,$unique_id,$t);

         
         
        
         $rcode = '1'; 
         $message = $jay;
         
           
           
        }
        else{
            $rcode='0';
            $message="this email has previously been used";
       
       
   }
  

}

    }
    $result = array('rcode'=>$rcode,'message'=>$message);
}


  
    
      

//LOGIN BLOCK

if($project['key']=='A346'){
    $allfields = array('surname','name','othername','email','password');
    $reqfields = array('email','password');
    $received = array();
    // for looping through non-associative arrays, you say foreach($data as $value), but for associative arrays, foreach($data as $key => $value)
    foreach ($project as $vv =>$value){
        array_push($received, $vv);
    }



    foreach ($reqfields as $ff){
        if(in_array($ff, $received)){
            $cont = TRUE;
        }else 
        {
            $error = TRUE;
            $rcode = '0'; 
            $message= "$ff.' '.'required'"; 
             break;}
    }
if(!isset($error)){
    //foreach ($allfields as $mm){
        //if ($mm =='email'){
    extract($project);
    if(isset($email)){       
            $vv=$validate->datatype('5', $email);
    }        
            if ($vv == '0') {
                $error = "";
                $rcode = '0'; 
                $message = "email is incorrect";
                //die();
            }
    
    
        if(isset($password)){//We are trying to check if $mm which is password meet the requirement
            $vv=$validate->datatype('4', $password);
        }    //We are trying to check if the field i.e password is in correct format 
        
            if ($vv=='0'){
                $error = true;
                $rcode = '0'; 
                $message = "incorrect password";
                //die();
        }




 //We check if there is no error
 if(!isset($error)){
    // extract email from the payload to check if email exists
    $email = $project['email']; 
    //Define table for the check
    $table="amahjosh";
//Define where clause for the check
    $where= "email='$email'";
    //define columns for check,, use only email here to save ram
    $col="*";
    // check if email exists.. send  table col and where to the sql library and call the select method
    $checkemail=$validate2->buildselect($table,$col,$where,'','1');
    //print_r($checkemail); die(); 
    //since this is login the number of rows numrow must be 1
   if($checkemail['numrows'] == 1){
       if($checkemail['ugozee99'][0]['PASSWORD']=== sha1(md5(($project['password'])))){
        $issuer='http://localhost:4200';
        $audience='http://localhost:4200/#/';
        $user_id=$project['email'];
        $unique_id= sha1('user_id');
        $tk=time() + 900;
        $jay=$forjwt::encryptjwt($issuer,$audience,$user_id,$unique_id,$tk);
        $rcode = '1';
        $message=$jay; 
          //die('');
       }else
       {
           $rcode = '0';
           $message="Check Login Credentails";
        }    
   }else
   { 
    $rcode = '0';
    $message="Invalid Login";
    }
 }
 $result = array('rcode'=>$rcode,'message'=>$message); 
}

}

//Merchant Sign up
if($project['key']=='A348'){
    $reqfields=array('School_Name','School_Type','School_Address','phone_number','service_id');
    $accept = array();
    foreach ($project as $vv=>$value){
     array_push($accept,$vv);
     //print_r($accept);die();  
    }

    foreach ($reqfields as $gg){
            if(in_array($gg,$accept)){
                $goahead=TRUE; 
            }else { 
                $rcode='0';
                $message= $gg." "."required"; 
                $error="";
                break;
            } 
    }
    if(!isset($error)){
        foreach($reqfields as $input){
            //var_dump($reqfields);die();
           if($input == 'School_Name'){       
              $vv=$validate->datatype('7', $project[$input]);
               if ($vv=='0'){ 
                   $error = "";
                   $rcode='0';
                   $message= $input[$vv]." "."not in the right format"; 
                   break;
                }      
            }
            if ($input == 'School_Type'){
                $vv=$validate->datatype('3', $project[$input]);
                if ($vv=='0'){
                     $error = "";
                     $rcode='0';
                     $message=$input[$vv]." ". "has to be a number";
                     break;
                }
            }
            if ($input == 'School_Address'){
                $vv=$validate->datatype('7', $project[$input]);
                if ($vv=='0'){
                    $error = "";
                    $rcode='0';
                    $message=$project[$input]." ". "not in the right format";   
                    break;
                }
            }

            if ($input == 'phone_number'){
                $vv=$validate->datatype('3', $project[$input]);
                if ($vv=='0'){ 
                    $error = "";
                    $rcode='0';
                    $message=$project[$input]." ". "not in right format"; 
                    break;
                }
            }
            if ($input == 'service_id'){
                $vv=$validate->datatype('1', $project[$input]);
                if ($vv=='0'){
                    $error = "";
                    $rcode = '0';
                    $message=$project[$input].""."not in right format";
                    break;
                }
            }
        }
    }

    if(!isset($error)){
        extract($project);
        $values = "School_Name='$School_Name', School_Type='$School_Type',
        School_Address='$School_Address',service_id='$service_id'";
        $email="josh@gmail.com";
        $set=$validate2->updatedb($values,$email);
        $createschool=TRUE;
        if(isset($createschool)){

            $table="myschool";
            $colinsert = "email,schooltype,schoolname,schooladdress,phone_number,service_id";
            $valinsert = " '$email','$School_Type','$School_Name','$School_Address','$phone_number','$service_id'";
            $insertdata = $validate2->buildinsert($table,$colinsert,$valinsert);
        }
        $rcode= "1";
        $message="good";
    }else{
        $rcode= "0";
        $message="bad";
    }
    $result=array('rcode'=>$rcode,'message'=>$message);
}
if($project['key']=='AB1'){
    //extract($project);
    $table="amahjosh";
    $col='EMAIL as em,SURNAME as su,NAME as na,OTHERNAME as oth,status as st,School_Name as schN,School_Type as schT,School_Address as schA,finishedprofile as fp';
    $where=" email= '$jwtuser' ";
    $checkemail=$validate2->buildselect($table,$col,$where,'','1');
    $rcode='1';
    $message = $checkemail;
    $result=array('rcode'=>$rcode,'message'=>$message);
    //var_dump($afterlogin);die();

    //print_r($b4dash);

    

} 
if($project['key']=='A400'){
    $requiredfields=array('merchant_address','phone_number');
    $accept = array();
    foreach($project as $vv=>$value){
        array_push($accept,$vv);  
    }
    foreach($requiredfields as $gg){
        if(in_array($gg,$accept)){
            $goahead=TRUE; 
        }else { 
            $error=TRUE;
            $rcode = '0';
            $message = $gg." "."required"; 
            break;
        } 
        //die();      
    }
        //echo "$goahead";
    if(!isset($error)){
        foreach($requiredfields as $input){
            //var_dump($reqfields);die();
            if($input == 'merchant_address'){
        
                $vv=$validate->datatype('2', $project[$input]);
                if ($vv=='0'){ 
                    $error = TRUE;
                    $rcode = '0';
                    $message = $vv." "."has to be a merchant address"; 
                    break;
                }
                
            }
        

            if($input == 'phone_number'){
                $vv=$validate->datatype('8', $project[$input]);
                if ($vv=='0'){ 
                    $error = TRUE;
                    $rcode = '0'; 
                    $message = $vv." ". "has to be in correct length"; 
                    break;
                }

            }
            if($input == 'phone_number'){
                $vv=$validate->datatype('9', $project[$input]);
                if($vv=='0'){
                    $error = TRUE; 
                    $rcode = '0';
                    $message = $vv." ". "has to be a numbers all through"; 
                    break;
                }

            }
        }
    }
    if(!isset($error)){
        extract($project);

        $values = "merchant_address='$merchant_address', phone_number='$phone_number'";
        $feeder= "bossy@gmail.com";
        $set=$validate2->updatedb($values,$feeder);

        $rcode='1';
        $message='';

        $update= TRUE;
        if(isset($update)){
            $value="finishedprofile='1'";
            $finished=$validate2->updatedb($value,$feeder);
        }

    }else{
        $rcode='0';
        $message='school already registered';
    }    
        
    
    $result=array('rcode'=>$rcode,'message'=>$message);
}








}else{die('the key dosent match');}
if ($result){
      echo json_encode ($result);
  }


























    



?>