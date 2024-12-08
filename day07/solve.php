<?php

function parse_nums($val) {
    return intval(trim($val));
}

function get_vals($val) {
    return is_numeric($val);
}

function get_data($file) {
    $raw = file_get_contents($file);


    $rows = explode("\n",$raw);

    $set = [];

    foreach($rows as $row) {
        if(empty($row))
            continue;
        $parts = explode(": ",trim($row));

        $ans = intval(trim($parts[0])); 

        $nums = explode(" ",trim($parts[1]));

        $nums = array_map("parse_nums",$nums);
        $nums = array_filter($nums,"get_vals");

        $set[] = [
            "answer" => $ans,
            "parts" => $nums,
        ];
    }
    return $set;
}

function get_result($res,$current,$parts,$past) {
    if(empty($parts)) {
        if($res == $current){
            array_shift($past);
            echo implode("",$past)." == ".$res."\n";
            return true;
        }
        return false;
    }
    $next = array_shift($parts);

    $past[] = "+";
    $past[] = $next;
    $sum = get_result($res,$current+$next,$parts,$past);
    array_pop($past);
    array_pop($past);
    $past[] = "*";
    $past[] = $next;
    $times = get_result($res,$current*$next,$parts,$past);


    if($sum == true) {
        return true;
    }
    if($times == true) {
        return true;
    }
    return false;
}


function first($file) {
    $data = get_data($file);

    $res = 0;
    foreach($data as $test) {
        if(get_result($test['answer'],0,$test['parts'],[])) {

            $res += $test['answer'];
        }
        else {
        }

    }

    echo "Answer is: ".$res."\n";
}

function get_result2($res,$current,$parts,$past) {

    if(empty($parts)) {
        if($res == $current){
            array_shift($past);
            echo implode("",$past)." == ".$res."\n";
            return true;
        }
        return false;
    }
    $next = array_shift($parts);

    $past[] = "+";
    $past[] = $next;
    $sum = get_result2($res,$current+$next,$parts,$past);
    array_pop($past);
    array_pop($past);
    $past[] = "*";
    $past[] = $next;
    $times = get_result2($res,$current*$next,$parts,$past);


    // operator
    array_pop($past);
    array_pop($past);
    $cat = false;
    if(empty($past) == false) {
        $past[] = "||";
        $past[] = $next;
        
        $current = intval("".$current.$next);

        $cat = get_result2($res,$current,$parts,$past);
    }


    if($sum == true) {
        return true;
    }
    if($times == true) {
        return true;
    }
    if($cat == true) {
        return true;
    }
    return false;
}


function second($file) {
    $data = get_data($file);

    $res = 0;
    foreach($data as $test) {
        if(get_result2($test['answer'],0,$test['parts'],[])) {

            $res += $test['answer'];
        }
        else {
        }

    }

    echo "Answer is: ".$res."\n";
}

// first("input.txt");
second("input.txt");





