<?php

$rules = [];

/* Infinite loop as the rules were not transitional. Lets just leave this here. 
function sort_numbers($a,$b) {
    if(find_value($a,$b)) {
        return -1;
    }
    return 1;
}
function find_value($a,$b,$seen=[]) {
    global $rules;

    echo $a."-> ";
    if(isset($rules[$a]) == false) {
        return false;
    }

    foreach($rules[$a] as $val) {
        //echo " ".$val." ";
        if($val == $b) {
            //echo "\n";
            return true;
        }

        if(find_value($val,$b)) {
            //echo "\n";
            return true;
        }
    }
    echo "\n";
    return false;
}
 */

function get_data($file) {
    global $rules;
    $raw = file_get_contents($file);

    $rows = explode("\n",$raw);

    $section=1;

    $pages = [];

    $numbers = [];

    foreach($rows as $row) {
        if(empty($row) and $section == 1) {
            $section = 2;
            continue;
        }
        elseif(empty($row)) {
            continue;
        }

        switch($section) {
            case 1:
                // parse ordering
                $parts = explode("|",$row);

                $numbers[$parts[0]] = $parts[0];
                $numbers[$parts[1]] = $parts[1];
                    

                $rules[] = [$parts[0],$parts[1]];

                break;
            case 2:
                // parse page lists.
                $list = explode(",",$row);

                $pages[]=$list;
                break;
            default:
                echo "WTF?\n";
                die();
                break;
        }
    }

    return ["numbers"=> $numbers,"rules"=>$rules,"pages"=>$pages];
}

function check_rule($list,$a,$b) {
    
    if(in_array($a,$list) and in_array($b,$list)) {
        $ai = array_search($a,$list);
        $bi = array_search($b,$list);

        if($ai < $bi) {
    //echo implode(",",$list)." $a before $b\n";
            return true;
        }
    //echo implode(",",$list)." $a not before $b\n";
        return false;
    }
    // no rules
    //echo implode(",",$list)." no $a $b in list\n";
    return true;
}

function first($file) {
    $set = get_data($file);

    //var_dump($set['rules']);
    $val = 0;
    foreach($set['pages'] as $pages) {

        $ok=true;
            foreach($set['rules'] as $rule ) {
                if(check_rule($pages,$rule[0],$rule[1]) == false) {
                    $ok =false;
                    break;
                }
            }

        if($ok) {
            $size = sizeof($pages);
            echo implode(",",$pages)." was ok\n";
            $mid_id = intVal($size/2);
            $val += $pages[$mid_id];


        }
        else {

            echo implode(",",$pages)." was broken\n";
        }
    }
    echo "Result: ".$val."\n";
}

function second($file) {
    $set = get_data($file);

    //var_dump($set['rules']);
    $val = 0;
    foreach($set['pages'] as $pages) {

        $fix=false;
        $redo=true;
        while($redo) {
            $redo=false;
            foreach($set['rules'] as $rule ) {
                if(check_rule($pages,$rule[0],$rule[1]) == false) {
                    $ia = array_search($rule[0],$pages);
                    $ib = array_search($rule[1],$pages);

                    $tmp = $pages[$ia];
                    $pages[$ia] = $pages[$ib];
                    $pages[$ib] = $tmp;
                    $fix=true;
                    $redo=true;
                    break;
                }
            }
        }

        if($fix) {
            $size = sizeof($pages);
            echo implode(",",$pages)." was fixed\n";
            $mid_id = intVal($size/2);
            $val += $pages[$mid_id];
        }
        else {

            echo implode(",",$pages)." was ok\n";
        }
    }
    echo "Result: ".$val."\n";
}

//first("input.txt");
second("input.txt");


