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

// screen has a hardcoded "main" buffer that. 

// origin is in top left. 
// child buffers have borders at -1 and +1 around the content. 
// this can be seen on test3 frame which is only one elemen 
// wide and high buffer at the very top left. 

// create 5 buffers. 
// test will have rolling text in its buffer.
$c->addChildBuffer("main","test",25,1,30,7);
// map will have random characters and colors to create a ascii "map"
$c->addChildBuffer("main","map",25,10,30,5);
// test 2 will have static content
$c->addChildBuffer("main","test2",10,7,30,5);
// test 3 will be positioned on top left with single cyan X character a the content.
$c->addChildBuffer("main","test3",0,0,1,1);
// test 4 will be positioned at bottom right with single red X.
$c->addChildBuffer("main","test4",-1,-1,1,1);

// First teh static buffers. 
$c->setBuffer("test3",0,0,"X","cyan");
$c->setBuffer("test4",0,0,"X","red");



$c->addChildBuffer("main","test5",1,10,10,10);
$c->addChildBuffer("test5","test6",1,1,2,2);


$c->setBuffer("test6",0,0,"X","green");


// text goest to bottom
$c->writeBuffer("test2","     Some static content");
$c->writeBuffer("test2","   Overlaps the stuff behind");
$c->writeBuffer("test2"," ");
$c->writeBuffer("test2"," ");

// random chgaracters and colors 
$ca=array("1"=>"X","2"=>".","3"=>"*");
$co=array("1"=>"red","2"=>"green","3"=>"blue","4"=>"black");

// buffers that will be updated every 0.5 seconds.
for ( $i = 0 ; $i < 10 ; $i++ ) {
    for ( $k = 0 ; $k <= 30 ; $k++ ) {
        for ( $j = 0 ; $j <= 5 ; $j++ ) {
            $r=$ca[rand(1,3)];
            $rc=$co[rand(1,4)];
            $c->setBuffer("map",$k,$j,$r,$rc);
        }
    }

    $c->writeBuffer("main","Testi ".$i);
    $c->writeBuffer("test",sprintf("Testi ".$green."Green".$reset." %d.",$i));
    $c->drawBuffer();

    usleep(500000);
}


