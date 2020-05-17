<?php 
// double transposition function - anhthy 174 final
 // logic from https://www.pbs.org/wgbh/nova/decoding/doubtrans.html
$content = "apples";
// $decrypt = "n egNuetg s LIeikCh kic";
$key = "3021";
$key2 = "2013";

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

function splitKey($keyInput)
{
    $key = array();
    for ($i = 0; $i < strlen($keyInput); $i ++) {
        $key[$i] = substr($keyInput, $i, 1);
    }
    return $key;
}

function double_trans($input, $key_str, $key2_str, $de)
{
    $output = "";
    $inputLen = strlen($input);
    $key = sortArray(splitKey($key_str));
    $key2 = sortArray(splitKey($key2_str));
    
    $col_size = count($key);
    $row_size = ceil($inputLen / $col_size);
    $arr = array();
    $count = 0;
    $input = preg_replace('/\s/u', '-', $input);

    // echo "<br><br>";

    // echo $used . "<br>";

    if ($de == "decrypt") {
        //set array with empty spaces first 
        $input_arr = splitKey($input); 
        $count = 0; 
        $arr = array();
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
        //print_table($arr);
        $new_arr = dec_cols($row_size, $col_size, $arr, $key2);
        $final_arr = dec_cols($row_size, $col_size, $new_arr, $key);
        
        for ($i = 0; $i < $row_size; $i ++) {
            for ($j = 0; $j < $col_size; $j ++) {
                $output = $output . $final_arr[$i][$j];
            }
        }
    } else {
        // echo "<h1>---------------------------ENCRYPT---------------------------</h1> <br><br>";
        // echo "transpose rows <br>";
        for ($row = 0; $row < $row_size; $row ++) {
            for ($col = 0; $col < $col_size; $col ++) {
                if ($count < $inputLen) {
                    $curr = $input[$count];
                    $arr[$row][$col] = $curr;
                    //echo $row . $col . $curr;
                } else {
                    $arr[$row][$col] = "-";
                }
                $count ++;
            }
        }
        // echo "<h1>---------------------------org---------------------------</h1> <br><br>";
        //print_table($arr);
        
        $new_arr = trans_cols($row_size, $col_size, $arr, $key);
        $final_arr = trans_cols($row_size, $col_size, $new_arr, $key2);

        for ($i = 0; $i < $row_size; $i ++) {
            for ($j = 0; $j < $col_size; $j ++) {
                $output = $output . $final_arr[$i][$j];
            }
        }
    }
    $output = str_replace('-', '&nbsp;', $output);
    return $output;
}

function trans_cols($row_size, $col_size, $new_arr, $key2)
{
    $final_arr = array();
    $new_str = "";
    for ($j = 0; $j < $col_size; $j ++) {
        $index = array_search($j, $key2); 
        
        for ($k = 0; $k < $row_size; $k ++) {
            if ($new_arr[$k][$index]=="-"){
                $k++;
            }
            else {
                $new_str .= $new_arr[$k][$index];
            }
            
        }
    }
    $final_arr = make_arr($new_str, $col_size, $row_size);
    //print_table($final_arr);
    return $final_arr;
}

function dec_cols($row_size, $col_size, $new_arr, $key2)
{
    $final_arr = array();
    $count = 0; 
    $output = ""; 
    $new_str =""; 
    for ($i = 0; $i < $row_size; $i ++) {
        for ($j = 0; $j < $col_size; $j ++) {
            if ($new_arr[$i][$j]!='-'){
                $output = $output . $new_arr[$i][$j];
            }
        }
    }
    
    for ($i = 0; $i < $col_size; $i ++) {
        $index = array_search($i, $key2);
        for ($k = 0; $k < $row_size; $k ++) {
            if ($new_arr[$k][$index]=="-"){
                $final_arr[$k][$index] = "-";
                $k++;
            }
            elseif ($count < strlen($output)) {
                $final_arr[$k][$index] = substr($output, $count, 1);
                $count++;
            }
            
        }
    }
    
    for ($i = 0; $i < $row_size; $i ++) {
        for ($j = 0; $j < $col_size; $j ++) {
            $new_str = $new_str . $final_arr[$i][$j];
        }
    }
    
    $final_arr = make_arr($new_str, $col_size, $row_size);
    //print_table($final_arr);
    return $final_arr;
}

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

$ans = double_trans($content, $key, $key2, "encrypt");
print $ans . "<br>";
$dec =  double_trans("selpap", $key, $key2, "decrypt");
print $dec . "<br>";

