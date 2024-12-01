<?php
/**
PHP console draw library test file.

Copyright 2020 Johannes Korpela
License: MIT


Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

require_once("draw.php");


// create a new screen
$c = new Screen();

// random chgaracters and colors 
$ca=array("1"=>"X","2"=>".","3"=>"*");
$co=array("1"=>"red","2"=>"green","3"=>"blue","4"=>"black");
$w = $c->getWidth();
$h = $c->getHeight();
$c->addChildBuffer("main","foo",0,0,40,1);

$start = hrtime(true);

// buffers that will be updated every 0.5 seconds.
for ( $i = 1 ; $i < 100 ; $i++ ) {
    for ( $k = 0 ; $k <= $w ; $k++ ) {
        for ( $j = 0 ; $j <= $h ; $j++ ) {
            $r=$ca[rand(1,3)];
            $rc=$co[rand(1,4)];
            $c->setBuffer("main",$k,$j,$r,$rc);
        }
    }

    $now = hrtime(true);

    $diff = $now - $start;
    $c->writeBuffer("foo","F ".$i." NS:".$diff." FPS:".($i/($diff/1000000000)));
    
    $c->drawBuffer();

    //usleep(1);
}


