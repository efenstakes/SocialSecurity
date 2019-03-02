<?php
 
 /**
    keep functions that are not specific to any object in the application
 **/
 class Utility{
    
    // initialize any class variables
    public function __construct(){}

    public static function getAlphanumerics($len = 8){
      $string = "";
      $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      
      for($i=0;$i<$len;$i++)
        $string .= substr($chars,rand(0,strlen($chars)),1);
      return $string;
    }


    public static function get_random($arr) {
      return $arr[array_rand($arr)];
    }

    // release any resources this instance is holding
    public function __destruct(){}


 }// end of class

?>