<?php
//logic from https://www.pbs.org/wgbh/nova/decoding/doubtrans.html
// $content = "I Like Chicken Nuggets";
// $decrypt = "n egNuetg s  LIeikCh kic";
// $key = "2301";
// $key2 = "120534";

function double_trans($input, $key, $key2, $de)
{
    $output = "";
    $inputLen = strlen($input);
    $row_size = 4;
    $col_size = ceil($inputLen / $row_size);
    $arr = array();
    $count = 0;
    $input = preg_replace('/\s/u', '-', $input);

    for ($row = 0; $row < $row_size; $row ++) {
        for ($col = 0; $col < $col_size; $col ++) {
            if ($count < $inputLen) {
                $curr = $input[$count];
                $arr[$row][$col] = $curr;
            } else {
                $arr[$row][$col] = "-";
            }
            $count ++;
        }
    }
    //echo "<h1>---------------------------org---------------------------</h1> <br><br>";
    //print_table($arr);

    //echo "<br><br>";

    // echo $used . "<br>";

    if ($de == "decrypt") {
        //echo "<h1>---------------------------DECRYPT---------------------------</h1> <br><br>";
        //echo "transpose cols <br>";
        $new_arr = array();
        for ($i = 0; $i < strlen($key2); $i ++) {
            $index = substr($key2, $i, 1);
            for ($j = 0; $j < $row_size; $j ++) {
                $new_arr[$j][$index] = $arr[$j][$i];
            }
        }
        //print_table($new_arr);
        //echo "<br><br>";

        //echo "transpose rows <br>";
        $dec_arr = trans_rows($row_size, $new_arr, $key);
        //print_table($dec_arr);

        //echo "<br><br>";

        for ($i = 0; $i < $row_size; $i ++) {
            for ($j = 0; $j < $col_size; $j ++) {
                $output = $output . $dec_arr[$i][$j];
            }
        }
    } else {
        //echo "<h1>---------------------------ENCRYPT---------------------------</h1> <br><br>";
        //echo "transpose rows <br>";
        $new_arr = trans_rows($row_size, $arr, $key);
        //print_table($new_arr);

        // transpose columns

        //echo "<br><br>";
        //echo "transpose cols <br>";
        $final_arr = trans_cols($row_size, $col_size, $new_arr, $key2);
        //print_table($final_arr);

        for ($i = 0; $i < $row_size; $i ++) {
            for ($j = 0; $j < $col_size; $j ++) {
                $output = $output . $final_arr[$i][$j];
            }
        }
    }
    $output = str_replace('-', '&nbsp;', $output);
    return $output;
}

function trans_rows($row_size, $arr, $key)
{
    $new_arr = array();
    $row = 0;
    for ($i = 0; $i < strlen($key); $i ++) {
        $index = substr($key, $i, 1);
        if ($index < $row_size) {
            $new_arr[$row] = $arr[$index];
            $row ++;
        }
    }

    return $new_arr;
}

function trans_cols($row_size, $col_size, $new_arr, $key2)
{
    $final_arr = array();
    // $col=0;
    // for ($i=0; $i<strlen($key2); $i++){
    // $index = substr($key2, $i, 1);
    // if ($index < $col_size){
    // $test_col = array_column($new_arr, $index);
    // for ($j=0; $j<$row_size; $j++){
    // $final_arr[$j][$col] = $test_col[$j][0];
    // //echo $test_col[$j][0];

    // }
    // $col++;
    // }
    // }
    for ($i = 0; $i < strlen($key2); $i ++) {
        $index = substr($key2, $i, 1);
        for ($j = 0; $j < $row_size; $j ++) {
            $final_arr[$j][$i] = $new_arr[$j][$index];
        }
    }
    return $final_arr;
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

// $ans = double_trans($content, $key, $key2, "encrypt");
// print $ans . "<br>";
// echo double_trans($decrypt, $key, $key2, "decrypt");

