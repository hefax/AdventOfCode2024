<?php


function filter($char) {
  $allowed = ["X","M","A","S"];

  if(in_array($char,$allowed)) 
    return $char;

  return ".";
}

function get_data($file) {
  $raw = file_get_contents($file);

  $rows = explode("\n",$raw);

  $arr = [];
  foreach($rows as $row) {
    if(empty($row)) 
      continue;

    $row = trim($row);
    $chars = mb_str_split($row);

    $arr[] = array_map("filter",$chars);
  }

  return $arr;
}

function print_map($map){
  
  $max_r = sizeof($map);
  $max_c = sizeof($map[0]);

  for($r=0; $r<$max_r;$r++) {
    for($c=0; $c<$max_c;$c++) {
      echo $map[$r][$c]."";
    }
    echo "\n";

  }

  echo "\n";

}


function empty_map($map){
  
  $max_r = sizeof($map);
  $max_c = sizeof($map[0]);
  $emp=[];

  for($r=0; $r<$max_r;$r++) {
    $row = [];
    for($c=0; $c<$max_c;$c++) {
      $row[] = ".";
    }
    $emp[] = $row;
  }

  return $emp;
}



function check_coords_XMAS($map,$x,$m,$a,$s) {
  $max_r = sizeof($map);
  $max_c = sizeof($map[0]);

  if(
     ($x[0] < 0 or $x[0] >= $max_r) or 
     ($m[0] < 0 or $m[0] >= $max_r) or 
     ($a[0] < 0 or $a[0] >= $max_r) or 
     ($s[0] < 0 or $s[0] >= $max_r) or
     ($x[1] < 0 or $x[1] >= $max_c) or 
     ($m[1] < 0 or $m[1] >= $max_c) or 
     ($a[1] < 0 or $a[1] >= $max_c) or 
     ($s[1] < 0 or $s[1] >= $max_c)
  ) {
    // overflow protection
    return false;
  }

  if(
    $map[$x[0]][$x[1]] == "X" and 
    $map[$m[0]][$m[1]] == "M" and 
    $map[$a[0]][$a[1]] == "A" and 
    $map[$s[0]][$s[1]] == "S" 
   )
  {
    return true;
  }

  return false;
}

function analyze_for_XMAS($map,$heat,$r,$c) {
  
  $count = 0;
  
  $opts = [
    [[$r,$c],[$r,$c+1],[$r,$c+2],[$r,$c+3]],
    [[$r,$c],[$r,$c-1],[$r,$c-2],[$r,$c-3]],
    [[$r,$c],[$r+1,$c],[$r+2,$c],[$r+3,$c]],
    [[$r,$c],[$r-1,$c],[$r-2,$c],[$r-3,$c]],
    [[$r,$c],[$r+1,$c+1],[$r+2,$c+2],[$r+3,$c+3]],
    [[$r,$c],[$r+1,$c-1],[$r+2,$c-2],[$r+3,$c-3]],
    [[$r,$c],[$r-1,$c+1],[$r-2,$c+2],[$r-3,$c+3]],
    [[$r,$c],[$r-1,$c-1],[$r-2,$c-2],[$r-3,$c-3]],
  ];




  foreach($opts as $test) {
    // right
    if(check_coords_XMAS($map,
      $test[0],
      $test[1],
      $test[2],
      $test[3]
    )) {

        $heat[$test[0][0]][$test[0][1]] = "X";
        $heat[$test[1][0]][$test[1][1]] = "M";
        $heat[$test[2][0]][$test[2][1]] = "A";
        $heat[$test[3][0]][$test[3][1]] = "S";
        $count +=1;
    }
  }

  return ["count" => $count, "map" => $heat];

}


function check_coords_XMAS_part2($map,$heat,$a) {
  $max_r = sizeof($map);
  $max_c = sizeof($map[0]);

  // for X-MAX we focus on A. and check
  if(
     ($a[0] < 0 or $a[0] >= $max_r) or 
     ($a[0]-1 < 0 or $a[0]-1 >= $max_r) or 
     ($a[0]+1 < 0 or $a[0]+1 >= $max_r) or 
     ($a[1] < 0 or $a[1] >= $max_c) or 
     ($a[1]+1 < 0 or $a[1]+1 >= $max_c) or 
     ($a[1]-1 < 0 or $a[1]-1 >= $max_c) 
  ) {
    // overflow protection
    return ["count" => 0, "map" => $heat];
  }

  if( $map[$a[0]][$a[1]] == "A") { 

    // tl - br 
    $tl_br = false;
    if(
      ($map[$a[0]-1][$a[1]-1] == "M" and 
      $map[$a[0]+1][$a[1]+1] == "S" ) or
      ($map[$a[0]-1][$a[1]-1] == "S" and 
      $map[$a[0]+1][$a[1]+1] == "M" ) ) {

      $tl_br = true;
    }

    $bl_tr = false;
    if(
      ($map[$a[0]+1][$a[1]-1] == "M" and 
      $map[$a[0]-1][$a[1]+1] == "S" ) or
      ($map[$a[0]+1][$a[1]-1] == "S" and 
      $map[$a[0]-1][$a[1]+1] == "M" ) ) {

      $bl_tr = true;
    }
    
    if($bl_tr and $tl_br) {
      $heat[$a[0]][$a[1]] = $map[$a[0]][$a[1]];
      $heat[$a[0]-1][$a[1]-1] = $map[$a[0]-1][$a[1]-1];
      $heat[$a[0]-1][$a[1]+1] = $map[$a[0]-1][$a[1]+1];
      $heat[$a[0]+1][$a[1]-1] = $map[$a[0]+1][$a[1]-1];
      $heat[$a[0]+1][$a[1]+1] = $map[$a[0]+1][$a[1]+1];


      return ["count" => 1, "map" => $heat];
    }
  }

  return ["count" => 0, "map" => $heat];
}

function first($file) {
  $map = get_data($file);
  print_map($map);

  $heat = empty_map($map);

  $max_r = sizeof($map);
  $max_c = sizeof($map[0]);

  $total = 0;

  for($r=0; $r<$max_r;$r++) {
    for($c=0; $c<$max_c;$c++) {
      $tmp = analyze_for_XMAS($map,$heat,$r,$c);
      $heat = $tmp['map'];
      $total += $tmp['count'];
    }
  }
  
  print_map($heat);
  echo "Total: ".$total."\n";


}
function second($file) {
  $map = get_data($file);
  print_map($map);

  $heat = empty_map($map);

  $max_r = sizeof($map);
  $max_c = sizeof($map[0]);

  $total = 0;

  for($r=0; $r<$max_r;$r++) {
    for($c=0; $c<$max_c;$c++) {
      $tmp = check_coords_XMAS_part2($map,$heat,[$r,$c]);
      $heat = $tmp['map'];
      $total += $tmp['count'];
    }
  }
  
  print_map($heat);
  echo "Total: ".$total."\n";


}
//first("input.txt");
second("input.txt");
