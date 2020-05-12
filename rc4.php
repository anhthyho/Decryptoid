<?php
//logic for rc4 from https://sites.math.washington.edu/~nichifor/310_2008_Spring/Pres_RC4%20Encryption.pdf 

//testing
//$str = "I Like Chicken Nuggets";
//$key = "1020123";

function rc4($key_str, $str, $de){
    // Store the vectors "S" has calculated
    
    
    $stage = array(); 
    
    if ($de=="decrypt"){
        $str = hex2bin($str);
    }
    $str_arr = array();
    for ($i=0; $i<strlen($str); $i++){
        $str_arr[$i] = substr($str, $i, 1);
    }
    
    $key_len = strlen($key_str);
    $key = array(); 
    for ($i=0; $i<$key_len; $i++){
        $key[$i] = substr($key_str, $i, 1);
    }
    
    
    for ($i=0; $i<256; $i++){
        $stage[$i] = $i;
    }
    
    $j=0;
    for ($i=0; $i<256; $i++){
        $j = (($j + $stage[$i] + $key[$i % $key_len]) % 256);
        $tmp = $stage[$j];
        $stage[$j] = $stage[$i];
        $stage[$i] = $tmp; 
    }
    
    $i=0; $j=0;
    $str_len = sizeof($str_arr);
    $res = array();
    $count = 0;
    for ($count=0; $count<$str_len; $count++){
        $i = ($i+1) % 256;
        $j= ($j+$stage[$i]) % 256;
        $tmp = $stage[$i];
        $stage[$i] = $stage[$j];
        $stage[$j] = $tmp;
        $t = ($stage[($stage[$i] + $stage[$j]) % 256]);
        $k = chr($stage[$t]);
        $ans = ($str_arr[$count]) ^ $k;
        if($de=="encrypt"){
            $ans = bin2hex($ans);
        }
        $res[$count] = $ans;
        //echo chr($ans);
        
    }
   // echo "<br>";
    $res = implode("", $res);
    return $res;
}

?>