<?php
//include 'class/generalop.php';
$data='12@';
if(preg_match("/[A-Z]/",$data) && preg_match("/[0-9]/",$data) && preg_match("/[!@]/",$data)){
            echo 'Good';
        }else{ echo "error";}    
    
?>