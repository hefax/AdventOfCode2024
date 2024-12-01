<?php

require_once("draw.php");


// create a new screen
$c = new Screen();


$c->addChildBuffer("main","panel1",1,1,40,-7);
$c->addChildBuffer("main","panel2",-42,1,40,-7);
$c->addChildBuffer("main","panel3",43,1,-44,-7);
$c->addChildBuffer("main","console",1,-5,-2,5);


$c->drawBuffer();
sleep(4);

