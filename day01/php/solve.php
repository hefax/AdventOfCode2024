<?php

function pois($var) {
  if(empty($var)) {
    return false;
  }
  return true;
}

function first($file) {
  $data = file_get_contents($file);

  $rows = explode("\n",$data);

  $set1 = [];
  $set2 = [];
  foreach($rows as $row) {
    $raw = explode(" ",$row );

    $pairs = array_filter($raw,"pois");

    if(empty($pairs)) {
      continue;
    }

    $set1[] = array_shift($pairs);
    $set2[] = array_shift($pairs);
  }

  sort($set1);
  sort($set2);

  $result = 0;
  while(true) {
    $first = array_shift($set1);

    if(is_null($first)) {
      break;
    }

    $second = array_shift($set2);

    $diff = abs($first-$second);

    echo "f: $first, s: $second, diff: $diff\n";

    $result+=$diff;
  }

  echo "Result: $result\n";
}

function second($file) {
  $data = file_get_contents($file);

  $rows = explode("\n",$data);

  $set1 = [];
  $set2 = [];
  foreach($rows as $row) {
    $raw = explode(" ",$row );

    $pairs = array_filter($raw,"pois");

    if(empty($pairs)) {
      continue;
    }

    $set1[] = array_shift($pairs);
    $set2[] = array_shift($pairs);
  }


  $result=0;

  $map = array_count_values($set2);

  foreach($set1 as $num) {
    $count = 0;
    if(isset($map[$num])) {
      $count = $map[$num];
    }

    $val = $num * $count;
    echo "$num => $count == ".$val."\n";

    $result += $val;

  }

  echo "Result: $result\n";
  



}

// first("input.txt");
second("input.txt");



