<?php
// double transposition function - anhthy 174 final
// logic from https://www.pbs.org/wgbh/nova/decoding/doubtrans.html
// examples and testing https://www.boxentriq.com/code-breaking/double-transposition-cipher

//testing purposes 
// $content = "i like chicken nuggets";
// $decrypt = "itggnu nkecich eikl se";
// $key = "1546";
// $key2 = "15321";

/**
 * sort array by order
 * @param $key 
 * @return number[] of key values
 */
function sortArray($key)
{
    $result = array();
    $temp = $key;
    sort($temp);
    
    for ($i = 0; $i < count($key); $i ++) {
        $result[$i] = - 1;
    }
    
    for ($i = 0; $i < sizeof($temp); $i ++) {
        for ($j = 0; $i < sizeof($key); $j ++) {
            if ($temp[$i] == $key[$j] && $result[$j] == - 1) {
                $result[$j] = $i;
                break;
            }
        }
    }
    return $result;
}

/**
 * split string into array
 * @param $keyInput
 * @return string[] of the string objects 
 */
function splitKey($keyInput)
{
    $key = array();
    for ($i = 0; $i < strlen($keyInput); $i ++) {
        $key[$i] = substr($keyInput, $i, 1);
    }
    return $key;
}

/**
 * double transposition 
 * @param initial text $input
 * @param key1 string $key_str
 * @param key2 string $key2_str
 * @param decrypt or encrypt marker $de
 * @return string output of decrypted/encrypted value
 */
function double_trans($input, $key_str, $key2_str, $de)
{
    $output = "";
    $inputLen = strlen($input);
    $key = sortArray(splitKey($key_str));
    $key2 = sortArray(splitKey($key2_str));
    
    //marker for spaces in the input 
    $input = preg_replace('/\s/u', '|', $input);
    
    
    if ($de == "decrypt") {
        // ---------------------------DECRYPT--------------------------- ;
        // create initial array and perform first transposition on key2 
        $col_size = count($key2);
        $row_size = ceil($inputLen / $col_size);
        $new_arr = dec_cols($row_size, $col_size, $input, $key2);
        
        // second transposition of decrypt using first key   
        $new_input = arr_to_string($new_arr, $row_size, $col_size);
        $final_arr = dec_cols($row_size, $col_size, $new_input, $key);
        
        //get and return output string value 
        $col_size = count($key);
        $row_size = ceil($inputLen / $col_size);
        $output = arr_to_string($final_arr, $row_size, $col_size);
        
    } else {
        // ---------------------------ENCRYPT--------------------------- ;
        
        // create initial array and perform first transposition on key(1)
        $col_size = count($key);
        $row_size = ceil($inputLen / $col_size);
        $new_arr = trans_cols($row_size, $col_size, $input, $key);
        
        // second transposition using key2 
        $new_input = arr_to_string($new_arr, $row_size, $col_size);
        $final_arr = trans_cols($row_size, $col_size, $new_input, $key2);
        
        //get and return output string value 
        $col_size = count($key2);
        $row_size = ceil($inputLen / $col_size);
        $output = arr_to_string($final_arr, $row_size, $col_size);
    }
    //clean the output with the spaces or extra - markers 
    $output = str_replace('-', '', $output);
    $output = str_replace('|', '&nbsp;', $output);
    return $output;
}

/**
 * encrypt columns -> key1 -> key2 
 * @param based on key size - $row_size
 * @param based on key size - $col_size
 * @param string to be passed into array $input
 * @param key value for this transposition $key2
 * @return  array of transposition 
 */
function trans_cols($row_size, $col_size, $input, $key2)
{
    
    $inputLen = strlen($input);
    //set array with empty spaces first
    $input_arr = splitKey($input);
    $count = 0;
    $arr = array();
    
    //create initial array based on size of passed key using existing input 
    $col_size = count($key2);
    $row_size = ceil($inputLen / $col_size);
    for ($i=0; $i<$row_size; $i++){
        for ($j=0; $j<$col_size; $j++){
            if ($count<$inputLen){
                $arr[$i][$j] = $input_arr[$count];
            }
            else {
                $arr[$i][$j] = "-";
            }
            $count++;
        }
    }
    
    //transpose values based on key 
    $final_arr = array();
    $new_str = "";
    for ($j = 0; $j < $col_size; $j ++) {
        $index = array_search($j, $key2);
        
        for ($k = 0; $k < $row_size; $k ++) {
            if ($arr[$k][$index]=="-"){
                $k++;
            }
            else {
                $new_str .= $arr[$k][$index];
            }
            
        }
    }
    //return transposed array 
    $final_arr = make_arr($new_str, $col_size, $row_size);
    //print_table($final_arr);
    return $final_arr;
}

/**
 * decrypt columns -> key2 -> key1
 * @param based on key size - $row_size
 * @param based on key size - $col_size
 * @param string to be passed into array $input
 * @param key value for this transposition $key2
 * @return  array of transposition
 */
function dec_cols($row_size, $col_size, $input, $key2)
{
    $inputLen = strlen($input);
    //set array with empty spaces first
    $input_arr = splitKey($input);
    $count = 0;
    $arr = array();
    
    //create initial array based on size of passed key using existing input 
    $col_size = count($key2);
    $row_size = ceil($inputLen / $col_size);
    for ($i=0; $i<$row_size; $i++){
        for ($j=0; $j<$col_size; $j++){
            if ($count<$inputLen){
                $arr[$i][$j] = $input_arr[$count];
            }
            else {
                $arr[$i][$j] = "-";
            }
            $count++;
        }
    }
    
    //transpose array by using key2 (output -> fill in the indiciated key index)
    $final_arr = array();
    $count = 0;
    $output = arr_to_string($arr, $row_size, $col_size);
    
    for ($i = 0; $i < $col_size; $i ++) {
        $index = array_search($i, $key2);
        for ($k = 0; $k < $row_size; $k ++) {
            if ($arr[$k][$index]=="-"){
                $final_arr[$k][$index] = "-";
                $k++;
            }
            elseif ($count < strlen($output)) {
                $final_arr[$k][$index] = substr($output, $count, 1);
                $count++;
            }
            
        }
    }
    
    // return final_array result of the transposition 
    $new_str = arr_to_string($final_arr, $row_size, $col_size);
    $final_arr = make_arr($new_str, $col_size, $row_size);
    return $final_arr;
}

/**
 * gets value of array to single string 
 * @param array to be split $new_arr
 * @param number rows to convert $row_size
 * @param number cols to print $col_size
 * @return string without special chars
 */
function arr_to_string($new_arr, $row_size, $col_size){
    $new_input = "";
    for ($i = 0; $i < $row_size; $i ++) {
        for ($j = 0; $j < $col_size; $j ++) {
            $new_input = $new_input . $new_arr[$i][$j];
        }
    }
    $new_input = str_replace('-', '', $new_input);
    return $new_input;
}

/**
 * turn string into array 
 * @param to turn into array $str
 * @param size of arr to make $col_size
 * @param size of arr to make $row_size
 * @return array of strings
 */
function make_arr($str, $col_size, $row_size)
{
    $str_arr = splitKey($str);
    $count = 0;
    $str_len = strlen($str);
    $res = array();
    
    for ($i = 0; $i < $row_size; $i ++) {
        for ($j = 0; $j < $col_size; $j ++) {
            if ($count < $str_len) {
                $res[$i][$j] = $str_arr[$count];
            } else {
                $res[$i][$j] = "-";
            }
            $count ++;
        }
    }
    return $res;
}

function generate_string($length)
{
    $random_string = "";
    for ($i = 0; $i < $length; $i ++) {
        $random_string = $random_string . $i;
    }
    $random_string = str_shuffle($random_string);
    return $random_string;
}

//prints the transposed tables - for testing purposes
function print_table($arr)
{
    print('<table border="1" cellpadding="10" cellspacing="10" style="border-collapse:collapse;">');
    for ($i = 0; $i < count($arr); $i ++) {
        print('<tr>');
        for ($ii = 0; $ii < count($arr[$i]); $ii ++) {
            print("<td>{$arr[$i][$ii]}</td>");
        }
        print('</tr>');
    }
    print('</table>');
}

// $key = generate_string(4);
// $key2 = generate_string(ceil(strlen($content) / 4));

// $ans = double_trans($content, $key, $key2, "encrypt");
// print $ans . "<br>";
// $dec =  double_trans($decrypt, $key, $key2, "decrypt");
// print $dec . "<br>";

