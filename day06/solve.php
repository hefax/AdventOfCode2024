<?php

function get_data($file) {
    $raw = file_get_contents($file);

    $rows = explode("\n",$raw);

    $map = [];
    foreach($rows as $row) {
        if(empty($row)) {
            continue;
        }
        $row=trim($row);

        $map[] = mb_str_split($row);
    }

    return $map;
}

function get_copy($map) {
    return array_values($map);
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
    
    if($r < 0 or $r >= $max_r or $c < 0 or $c >= $max_c) {
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


function get_path($map,$r,$c) {
    $UP = 1;
    $DOWN = 2;
    $LEFT = 4;
    $RIGHT = 8;

    $blank = get_blank($map);
    $d_blank = get_blank($map);

    $loop=true;
    $d = "UP";

    while($loop) {
        $blank[$r][$c] += 1;

        $d_symbol=0;
        switch($d) {
            case "UP":
                $d_symbol=$UP;
                break;
            case "DOWN":
                $d_symbol=$DOWN;
                break;
            case "LEFT":
                $d_symbol=$LEFT;
                break;
            case "RIGHT":
                $d_symbol=$RIGHT;
                break;
        }    

        if($d_blank[$r][$c] == 0) {
            $d_blank[$r][$c] = $d_symbol;
        }
        elseif(($d_blank[$r][$c] & $d_symbol) == $d_symbol) {
            // echo $d_blank[$r][$c]." + ".$d_symbol."== ".($d_blank[$r][$c] & $d_symbol) ."\n";
            // we have been here already going this direction. 
            // This is an infinite loop.
            // print_map($d_blank);
            return "INFINITE";
        }
        else {
            // add the direction here. 
            $d_blank[$r][$c] |= $d_symbol;
        }

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

    return $blank;
} 



function first($file) {
    $map = get_data($file);
    

    $start = find_start($map);

    $r = $start["row"];
    $c = $start["col"];

    $blank = get_path($map,$r,$c);

    $count = 0;
    foreach($blank as $row) {
        foreach($row as $node) {
            if($node > 0) {
                $count +=1;
            }
        }
    }
    print_map($map);
    echo "Result: ".$count."\n";

}

function second($file) {

    $map = get_data($file);
    
    $start = find_start($map);

    $r = $start["row"];
    $c = $start["col"];

    $map[$r][$c]=".";

    $blank = get_path($map,$r,$c);

    // var_dump($blank);



    $res = 0;
    foreach($blank as $rt => $row) {
        foreach($row as $ct => $node) {
            if($node == 0) {
                continue;
            }
            elseif($rt == $r and $ct == $c) {
                continue;
            }

            $copy = get_copy($map);
            $copy[$rt][$ct] = "#";

            $count = get_path($copy,$r,$c);
            if($count == "INFINITE") {
                $res +=1;
            }
        }
    }

    echo "Result: ".$res."\n";



}

first("input.txt");
second("input.txt");
