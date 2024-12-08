<?php

function get_data($file) {
    $raw = file_get_contents($file);

    $rows = explode("\n",$raw);

    $map=[];
    foreach ($rows as $row) {
        if(empty($row)) {
            continue;
        }

        $map[] = mb_str_split(trim($row));
    }

    return $map;
}

function copy_map($map) {
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

function get_list_of_towers($map) {

    $rows = sizeof($map);
    $cols = sizeof($map[0]);

    $towers = [];
    for($r=0;$r<$rows;$r++) {
        for($c=0;$c<$cols;$c++) {
            $node = $map[$r][$c];

            if($node == ".") {
                continue;
            }

            if(isset($towers[$node]) == false) {
                $towers[$node] =[];

            }
            $towers[$node][] = ["row"=>$r,"col"=>$c];
        }
    }

    return $towers;
}

function get_antinodes($list) {
    $antinodes = [];
    foreach($list as $frequency => $towers) {
        $count = sizeof($towers);

        for($i = 0; $i < sizeof($towers);$i++) {
            for($j = $i+1; $j < sizeof($towers); $j++) {
                $rd = $towers[$i]["row"] - $towers[$j]["row"];
                $cd = $towers[$i]["col"] - $towers[$j]["col"];

                $ar1 = $towers[$i]["row"] + $rd; 
                $ar2 = $towers[$j]["row"] - $rd; 
                $ac1 = $towers[$i]["col"] + $cd; 
                $ac2 = $towers[$j]["col"] - $cd; 

                $antinodes[] = ["row"=>$ar1,"col"=>$ac1];
                $antinodes[] = ["row"=>$ar2,"col"=>$ac2];
            }
        }
    }
    return $antinodes;
}

function filter_antinodes($map,$antinodes) {
    $rows = sizeof($map);
    $cols = sizeof($map[0]);

    $real_ones = [];

    $copy = get_blank($map);
    foreach($antinodes as $anti){
        if($anti["row"] < 0 or $anti["row"] >= $rows or 
            $anti["col"] < 0 or $anti["col"] >= $cols) {
            continue;
        }

        // 
        if($copy[$anti["row"]][$anti["col"]] > 0) {
            $copy[$anti["row"]][$anti["col"]] +=1;
            continue;
        }

        $real_ones[] = array_values($anti);

        if($map[$anti["row"]][$anti["col"]] == ".") {
            $map[$anti["row"]][$anti["col"]] = "#";
        }

        $copy[$anti["row"]][$anti["col"]] +=1;
        
    }

    return ["nodes" =>  $real_ones,"map"=>$map];
}


function first($file) {
    $map = get_data($file);

    print_map($map);

    echo "\n";
    $towers = get_list_of_towers($map);

    $antinodes = get_antinodes($towers);

    $final = filter_antinodes($map,$antinodes);

    print_map($final['map']);
    echo "\n";

    echo "End result: ".sizeof($final['nodes'])."\n";

}
function get_resonate_antinodes($list,$map) {
    $rows = sizeof($map);
    $cols = sizeof($map[0]);

    $antinodes = [];
    foreach($list as $frequency => $towers) {
        $count = sizeof($towers);

        for($i = 0; $i < sizeof($towers);$i++) {
            for($j = $i+1; $j < sizeof($towers); $j++) {
                $rd = $towers[$i]["row"] - $towers[$j]["row"];
                $cd = $towers[$i]["col"] - $towers[$j]["col"];

                $iter_r = $towers[$i]["row"]; 
                $iter_c = $towers[$i]["col"]; 
                while(true) {
                    if($iter_r < 0 or $iter_r >= $rows or 
                    $iter_c < 0 or $iter_c >= $cols) {
                        break;
                    }
                    $antinodes[] = ["row"=>$iter_r,"col"=>$iter_c];

                    $iter_r += $rd;
                    $iter_c += $cd;
                }

                $iter_r = $towers[$i]["row"] - $rd; 
                $iter_c = $towers[$i]["col"] - $cd; 
                while(true) {
                    if($iter_r < 0 or $iter_r >= $rows or 
                    $iter_c < 0 or $iter_c >= $cols) {
                        break;
                    }
                    $antinodes[] = ["row"=>$iter_r,"col"=>$iter_c];

                    $iter_r -= $rd;
                    $iter_c -= $cd;
                }
            }
        }
    }
    return $antinodes;
}



function second($file) {
    $map = get_data($file);

    print_map($map);

    echo "\n";
    $towers = get_list_of_towers($map);

    $antinodes = get_resonate_antinodes($towers,$map);

    $final = filter_antinodes($map,$antinodes);

    print_map($final['map']);
    echo "\n";

    echo "End result: ".sizeof($final['nodes'])."\n";

}

//first("input.txt");
second("input.txt");


