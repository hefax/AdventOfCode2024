<?php

function get_data($file) {
    $raw = file_get_contents($file);

    $rows = explode("\n",$raw);

    $reports=[];
    foreach ($rows as $row) {
        $items = explode(" ",$row);

        $report=[];
        foreach ($items as $item) {
            $tmp = trim($item);

            if (empty($item)) {
                continue;
            }

            $report[]=intVal($item);
        }

        if (empty($report) == false ){
            $reports[]=$report;
        }
    }
    return $reports;
}

function print_report($row,$res) {
    $dat = implode(" ",$row);
    echo $dat.": ".$res."\n";
}

function first($file) {
    $data = get_data($file);

    $safe = 0;
    $unsafe = 0;
    foreach ($data  as $report) {

        $status = is_safe($report);
        if ($status['safe']) {
            print_report($report,"Safe");
            $safe +=1;
        }
        else {
            print_report($report,"Unsafe: ".$report[$status['index']]." - ".$report[$status['index']+1]);
            $unsafe +=1;
        }

    }

    echo "We got $safe safe reports, and $unsafe unsafe reports.";
}


function second($file) {
    $data = get_data($file);

    $safe = 0;
    foreach ($data  as $report) {

        $status = is_safe($report);

        if ($status['safe']) {
            print_report($report,"Safe");
            $safe +=1;
        }
        else {
            $ok = false;
            $tests = [-1,0,+1];

            foreach ($tests as $offset) {
                $check = $status['index'] + $offset;
                if (isset($report[$check])) {
                    $a = $report;
                    unset($a[$check]);
                    $a = array_values($a);

                    $test = is_safe($a);

                    if ($test['safe'] == true) {
                        print_report($report,"Safe after removing: ".$report[$check]);
                        $safe +=1;
                        $ok = true;
                        break;
                    }
                }
            }

            if($ok == false ) {
                print_report($report,"Unsafe ".$report[$status['index']]." ");
            }
        }
    }

    echo "We got $safe safe reports.";
}


function is_safe($report) {
    $iter = 0;

    $size = sizeof($report);

    $direction = 0;

    while ($iter < $size -1) {

        $current = $report[$iter];
        $next = $report[$iter+1];
        $diff = $current - $next;

        if ($diff == 0) {
            return ["safe" => false,"index"=> $iter];
        }

        if ($direction == 0) {
            if($diff < 0) {
                $direction = -1;
            }
            else {
                $direction = 1;
            }
        }

        if ($direction > 0) {
            if ($diff > 0 and $diff <= 3) {
                // ok 
            }
            else {
                return ["safe" => false,"index"=> $iter];
            }
        }
        else {
            if ($diff < 0 and $diff >= -3) {
                // ok
            }
            else {
                return ["safe" => false,"index"=> $iter];
            }
        }
        $iter +=1;
    }
    return ["safe" => true];
}

first("test.txt");
first("input.txt");
second("test.txt");
second("input.txt");
