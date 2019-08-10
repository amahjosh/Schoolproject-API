<?php
//Require the jwt
require('jwt/JWT.php');
//Define a secret key
define('SECRET_KEY','getoffyourolympianheightandlearn');
use Firebase\JWT\JWT;

class HandleJwt extends JWT//we made a class, extended the class to the JWT.php library class
{    public function __construct()
   {
   }
   /**
    * This method decrypts the token and returns the user id inside the token
    */
   public static function openTokenfull($token)
   {
       //$jwt = new JWT;
       $decoded = self::decode($token, SECRET_KEY, array('HS256'));
       $decoded_array = (array)$decoded;
       $user = $decoded_array;
       return $user;
   }
   /**
    * This method decrypts the token and returns the user id inside the token
    */
   public static function openToken($token)
   {
       //$jwt = new JWT;
       $decoded = self::decode($token, SECRET_KEY, array('HS256'));
       $decoded_array = (array)$decoded;
       $user = $decoded_array['id'];
       return $user;
   }   
    //I TAKE THE DECRYPTED JWT TOKEN AS AN ARGUMENT
   public static function checkifcookieisvalid($decryptedJwtArray)
   {
       $expiry_time = $decryptedJwtArray['destruct'];
       return ($expiry_time > time()) ? 
       true : false;
   }



   /**ENCRYPT JWT METHOD */
   public static function encryptJwt($issuer, $audience, $user_id, $unique_id, $tk)
   {          // This method parameters will be coming in from where you will be calling the method from which will be from the login code or the signup code.
     
    //this is the key that you define on top of this file. 
    $key = SECRET_KEY;
       $token = array(
           "iss" => $issuer,
           "aud" => $audience,
           "id" => $user_id,
           'u_id' => $unique_id,
           'destruct' => $tk,
           "iat" => time(),
           "nbf" => time()
       );
       $hu = new JWT;
       $jwt = $hu::encode($token, $key);
       return $jwt;
   //echo json_encode($jwt);
   }
}

?>