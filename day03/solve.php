<?php

//xmul(2,4)%&mul[3,7]!@^do_not_mul(5,5)+mul(32,64]then(mul(11,8)mul(8,5))
//
//

function get_data($file) {
  return trim(file_get_contents($file));
}

function get_muls($data) {
  $err = preg_match_all('/mul\(([0-9]|[0-9][0-9]|[0-9][0-9][0-9]),([0-9]|[0-9][0-9]|[0-9][0-9][0-9])\)/',
    $data,
    $parts,
  PREG_PATTERN_ORDER);
  //([0-9]|[0-9][0-9]|[0-9][0-9][0-9]),var_dump($parts);

  return $parts;
}


function first($file) {
  $data = get_data($file);

  $parts = get_muls($data);

  $sum = 0;
  $size = sizeof($parts[0]);

  for ($i=0; $i<$size ; $i++) {
      $t = intVal($parts[1][$i])*intVal($parts[2][$i]);
      echo $parts[1][$i]." * ".$parts[2][$i]. " = ".$t."\n";
      $sum += $t;
  }

  echo $sum;
}

function filter($string) {
  while(true) {
   // echo "START: ".$string."\n";
    $start = strpos($string,"don't()");

    if($start == false) {
      break;
    }

    $stop = strpos($string,"do()",$start);


    $s1 = substr($string,0,$start);
    $end = "";

    if($stop != false ){
      $s2 = substr($string,$stop+4);

    }

    echo $start." - ". $stop." ".strlen($s1)."+".strlen($s2)."\n";


    $l1 = strlen($string);
    $string = $s1.$s2;
    $l2 = strlen($string);
    echo "Filter: ".$l2." \n";
   // echo "END: ".$string."\n";
  }

  return $string;
}


function second($file) {
  $data = get_data($file);

  $data = filter($data);





  $parts = get_muls($data);

  $sum = 0;
  $size = sizeof($parts[0]);

  for ($i=0; $i<$size ; $i++) {
      $t = intVal($parts[1][$i])*intVal($parts[2][$i]);
      echo $parts[1][$i]." * ".$parts[2][$i]. " = ".$t."\n";
      $sum += $t;
  }

  echo $sum;
}

//first("test.txt");
//first("input.txt");
//second("test2.txt");
second("input.txt");
