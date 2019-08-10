<?php
$dinma = new mysqli('localhost', 'root', '', 'schoolproject');
if($dinma == FALSE) {
    die('ERROR: could not connect');
}else{echo 'connection succ';}
var_dump ($dinma);
?>