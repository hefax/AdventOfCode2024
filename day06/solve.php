<?php

function get_data($file) {
    $raw = file_get_contents($file);

    $rows = explode("\n",$raw);

    $map = [];
    foreach($rows as $row) {
        if(empty($row)) {
            continue;
        }

        $map[] = mb_str_split($row);
    }

    return $map;
}


function get_blank($map) {
    $rows = sizeof($map);
    $cols = sizeof($map[0]);

    $new = [];
    for($r=0;$r<$rows;$r++) {
        $row = [];

        for($c=0;$c<$cols;$c++) {
            $row[]=0;
        }
        $new[]=$row;
    }

    return $new;
}

function find_start($map) {
    foreach($map as $r=> $row) {
        if(in_array("^",$row)) {
            $c = array_search("^",$row);

            return ["row"=>$r,"col"=>$c];
        }
    }
    return false;
}

function print_map($map) {
    $rows = sizeof($map);
    $cols = sizeof($map[0]);

    $new = [];
    for($r=0;$r<$rows;$r++) {

        for($c=0;$c<$cols;$c++) {
            echo $map[$r][$c];
        }
        echo "\n";
    }

}

function what_is_ahead($map,$r,$c) {
    $max_r = sizeof($map);
    $max_c = sizeof($map[0]);
    
    if($r < 0 or $r >= $max_r or $c < 0 or $c > $max_c) {
        return "OOB";
    }

    if($map[$r][$c] == "#") {
        return "OBJECT";
    }

    if(
        $map[$r][$c] == "." or
        $map[$r][$c] == "^"
    ) {
        return "FREE";
    }

    return "PANIC";
}

function first($file) {
    $map = get_data($file);
    
    $max_r = sizeof($map);
    $max_c = sizeof($map[0]);




    $blank = get_blank($map);

    $start = find_start($map);

    $r = $start["row"];
    $c = $start["col"];
    $d = "UP";

    $loop=true;

    while($loop) {
        $blank[$r][$c] += 1;

        $decide=true;
        while($decide) {
            $next_r = $r;
            $next_c = $c;

            switch($d) {
                case "UP":
                    $next_r-=1;
                break;
                case "DOWN":
                    $next_r+=1;
                break;
                case "LEFT":
                    $next_c-=1;
                break;
                case "RIGHT":
                    $next_c+=1;
                break;
            }    

            $check = what_is_ahead($map,$next_r,$next_c);

            switch($check) {
                case "OOB":
                    // we are out of the map
                    $loop=false;
                    $decide=false;
                    break;
                case "OBJECT":
                    switch($d) {
                        case "UP":
                            $d="RIGHT";
                        break;
                        case "DOWN":
                            $d="LEFT";
                        break;
                        case "LEFT":
                            $d="UP";
                        break;
                        case "RIGHT":
                            $d="DOWN";
                        break;
                    }
                    break;
                case "FREE":
                    $r=$next_r;
                    $c=$next_c;
                    $decide=false;
                    break;
                case "PANIC";
                    print_map($map);
                    print_map($blank);
                    die("PANIC");
                    break;
            }
        }
    }
    print_map($map);
    print_map($blank);

    $count = 0;
    foreach($blank as $row) {
        foreach($row as $node) {
            if($node > 0) {
                $count +=1;
            }
        }
    }

    echo "Result: ".$count."\n";

}

first("input.txt");
