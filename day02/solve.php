<?php

function get_data($file) {
  $raw = file_get_contents($file);

  $rows = explode("\n",$raw);

  $reports=[];
  foreach($rows as $row) {
    $items = explode(" ",$row);

    $report=[];
    foreach($items as $item) {
      $tmp = trim($item);

      if(empty($item)) {
        continue;
      }

      $report[]=intVal($item);
    }

    if(empty($report) == false ){
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
  foreach($data  as $report) {
    $last = null;
    $direction = 0;
    $ok=true;



    foreach($report as $level) {
      if( is_null($last) ) {
        $last = $level;
        continue;
      }
      else {
        $diff = $last - $level;

        if($direction == 0) {
          if($diff < 0) {
            $direction = -1;
          }
          elseif($diff > 0) {
            $direction = 1;
          }
          else {
            print_report($report,"Unsafe because diff was zero while determining the direction. $diff $direction");
            $unsafe+=1;
            $ok=false;
            break;
          }
        }
      }


      if($direction > 0) {
        if($diff > 0 and $diff <= 3) {
          
        }
        else {
          print_report($report,"Unsafe becase level diffs were wrong for increase. ($diff)");
          $unsafe +=1;
          $ok=false;
          break;
        }
      }
      else {
        if($diff < 0 and $diff >= -3) {
          
        }
        else {
          print_report($report,"Unsafe becase level diffs were wrong for decrease. ($diff)");
          $unsafe +=1;
          $ok=false;
          break;
        }
      }
      $last = $level;
    }

    if($ok) {
      print_report($report,"Safe");
      $safe += 1;
    }

  }

  echo "We got $safe safe reports, and $unsafe unsafe reports.";


}

function second($file) {
  $data = get_data($file);

  $safe = 0;
  foreach($data  as $report) {
    $last = null;
    $direction = 0;
    $ok=true;

    $unsafe = 0;

    $rdata=[];

    foreach($report as $level) {
      if( is_null($last) ) {
        $last = $level;
        continue;
      }
      else {
        $diff = $last - $level;

        if($direction == 0) {
          if($diff < 0) {
            $direction = -1;
          }
          elseif($diff > 0) {
            $direction = 1;
          }
          else {
            $unsafe+=1;
            if($unsafe > 1) {
              print_report($report,"Unsafe because diff was zero while determining the direction. $diff $direction");
              $ok=false;
              break;
            }
            echo "(ignoring start) ";
            continue;
          }
        }
      }


      if($direction > 0) {
        if($diff > 0 and $diff <= 3) {
          
          $rdata[] = $level.": ".$diff;
        }
        else {
          $unsafe +=1;
          if($unsafe > 1) {
            print_report($report,"Unsafe becase level diffs were wrong for increase. ($diff)");
            $ok=false;
            break;
          }
          echo "Ignoring ($level) ";
          continue;
        }
      }
      else {
        if($diff < 0 and $diff >= -3) {
          
          $rdata[] = $level.": ".$diff;
        }
        else {
          $unsafe +=1;
          if($unsafe > 1) {
            print_report($report,"Unsafe becase level diffs were wrong for decrease. ($diff)");
            $ok=false;
            break;
          }
          echo "Ignoring ($level) ";
          continue;
        }
      }
      $last = $level;
    }

    if($ok) {
      print_report($report,"Safe");
      $safe += 1;
    }

  }

  echo "We got $safe safe reports, and $unsafe unsafe reports.";


}

//first("input.txt");
second("input.txt");
