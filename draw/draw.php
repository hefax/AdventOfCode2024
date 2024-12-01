<?php
/**
PHP console draw library.

Copyright 2020 Johannes Korpela
License: MIT


Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

$red = '\e[31m';
$yellow = '\e[33m';
$blue = '\e[34m';
$green = '\e[92m';
$cyan = '\e[96m';
$reset = '\e[0m';


class Screen
{

    private $screen = [
        "width"=>0,
        "height"=>0,
        ];

    private $buffers = [];

    private $colors = [
            "red" => "\e[31m",
            "yellow" => "\e[33m",
            "blue" => "\e[34m",
            "green" => "\e[92m",
            "cyan" => "\e[96m",
            "reset" => "\e[0m",
        ];
    
    // W offset to not write over the right border
    private $w_offset = 0;
    // H offset to limit the height a bit to keep the results clean.
    private $h_offset = 4;


    public function __construct()
    {
        $this->getScreenSize();

        $this->buffers["main"] = [
            "content" => [],
            "children" => [],
            "str_buffer" => [],
            "parent" => null,
            "x" => 0,
            "y" => 0,
            "w" => $this->screen["width"],
            "h" => $this->screen["height"],
            "border" => false,
            ];

        // hide the cursor
        system("printf '\e[?25l'");
        // clear the screen
        system('clear');
    }

    public function __destruct()
    {
        // show the cursor
        system("printf '\e[?25h'");
    }

    public function getHeight()
    {
        return $this->screen["height"];
    }

    public function getWidth()
    {
        return $this->screen["width"];
    }
    
    /**
    * Create a new writing buffer at given location
    */
    public function addChildBuffer($parent, $name, $x, $y, $w, $h)
    {
        if(isset($this->buffers[$parent]) == false) {
            return false;
        }


        $this->buffers[$parent]["children"][$name] = $name;
        $parent = $this->buffers[$parent];

        if ($x < 0) {
            $x = $parent["w"] + $x;
        }

        $x += $parent["x"];

        if ($y < 0) {
            $y = $parent["h"] + $y;
        }
        
        $y += $parent["y"];

        if ($w < 0) {
            // in case of negative numbers we treat the request as 
            // relative to 
            $w =  $parent["w"] -$x + $w;
        }

        if ($h < 0) {
            $h = $parent["h"] -$y + $h;
        }
            
        $this->buffers[$name] = [
            "content" => [],
            "children" => [],
            "str_buffer" => [],
            "parent" => $parent,
            "x" => $x,
            "y" => $y,
            "w" => $w,
            "h" => $h,
            "border" => true,
        ];
    }

    /**
    * Set the screen size.
    */
    private function getScreenSize()
    {
        preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $output);

        if (sizeof($output) == 3) {
            // Width one column less to prevent warping
            $this->screen['width'] = $output[2][0]-$this->w_offset;
            // Lets cut 5 rows to keep the content on the screen once we finish.
            // Completely a personal preference here.
            $this->screen['height'] = $output[1][0]-$this->h_offset;
            $this->buffers["main"]["w"] = $this->screen['width'];
            $this->buffers["main"]["h"] = $this->screen['height'];
        }
    }

    /**
    * This draws the given buffer and its child buffers into data array that is given as a parameter
    * This is a private function that will be called from drawBuffer which uses it to get the final buffer
    * for echoing it to the screen.
    */
    private function drawElement($data, $name)
    {
        $w=$this->buffers[$name]["w"];
        $h=$this->buffers[$name]["h"];
        $x=$this->buffers[$name]["x"];
        $y=$this->buffers[$name]["y"];
    
        if ($this->buffers[$name]["border"] == true) {
            for ($iy = $h; $iy >= -1; --$iy) {
                if ($iy == $h or $iy == -1) {
                    for ($ix = -1; $ix <= $w; ++$ix) {
                        if (isset($data[$ix+$x]) == false) {
                            $data[$ix+$x]=[];
                        }
                        if ($ix==-1 and $iy == $h) {
                            // bottom left
                            $data[$ix+$x][$iy+$y] = "\xE2\x94\x97";
                        } elseif ($ix == $w and $iy == $h) {
                            // bottom right
                            $data[$ix+$x][$iy+$y] = "\xE2\x94\x9B";
                        } elseif ($ix== -1 and $iy == -1) {
                            // top left
                            $data[$ix+$x][$iy+$y] = "\xE2\x94\x8F";
                        } elseif ($ix == $w and $iy == -1) {
                            // top right
                            $data[$ix+$x][$iy+$y] = "\xE2\x94\x93";
                        } else {
                            $data[$ix+$x][$iy+$y] = "\xE2\x94\x81";
                        }
                    }
                } else {
                    if (isset($data[$x-1]) == false) {
                        $data[$x-1]=[];
                    }
                    if (isset($data[$x+$w]) == false) {
                        $data[$x+$w]=[];
                    }

                    $data[$x-1][$iy+$y] = "\xE2\x94\x83";
                    $data[$x+$w][$iy+$y] = "\xE2\x94\x83";
                }
            }
        }

        for ($iy = $h-1; $iy >= 0; --$iy) {
            for ($ix = 0; $ix < $w; ++$ix) {
                if (isset($data[$ix+$x]) == false) {
                    $data[$ix+$x]=[];
                }

                $data[$ix+$x][$iy+$y] = @$this->buffers[$name]["content"][$ix][$iy];
            }
        }

        foreach ($this->buffers[$name]["children"] as $chi) {
            $data = $this->drawElement($data, $chi);
        }
        return $data;
    }
    
    /**
    * With this the user will essentially echo text to a given buffer
    */
    public function writeBuffer($name, $str)
    {
        array_unshift($this->buffers[$name]["str_buffer"], $str);

        if (sizeof($this->buffers[$name]["str_buffer"])-1 > $this->buffers[$name]["h"]) {
            array_pop($this->buffers[$name]["str_buffer"]);
        }

        $this->applyBuffer($name);
    }

    /**
    * With this function the user will set an exact character in a given content array of a buffer
    *
    */
    public function setBuffer($name, $x, $y, $buf, $color = "black")
    {
        if (isset($this->buffers[$name]["content"][$x]) == false) {
            $this->buffers[$name]["content"][$x] = [];
        }

        $h = $this->buffers[$name]["h"];

        if (isset($this->colors[$color])) {
            $this->buffers[$name]["content"][$x][$y] = $this->colors[$color].$buf."\e[0m";
        } else {
            $this->buffers[$name]["content"][$x][$y] = $buf;
        }
    }

    /**
    * This will empty the stringbuffer and wipe the buffer clean.
    */
    public function resetBuffer($name)
    {
        $this->buffers[$name]["str_buffer"] = [];
        $this->applyBuffer($name);
    }

    /**
    * The strings that are written to a buffer are kept in their separate array. When we want to apply them
    * to the content array we will call the applyBuffer function that will loop through the strings, split
    * them and apply the charaters on the content array.
    *
    */
    private function applyBuffer($name)
    {
        // reset.
        $this->buffers[$name]["content"] = [];
        $rc=1;

        foreach ($this->buffers[$name]["str_buffer"] as $row) {
            $cs = str_split($row);
            
            $x = $this->buffers[$name]["x"];
            $y = $this->buffers[$name]["y"];
            $w = $this->buffers[$name]["w"];
            $h = $this->buffers[$name]["h"];

            
            $index = 0;
            $buf="";
            for ($i = 0; $i < sizeof($cs); $i++) {
                if ($cs[$i] == '\\' and $cs[$i+1] =='e' and $cs[$i+2] == "[") {
                    $buf = "\e[";

                    if (is_numeric($cs[$i+3])) {
                        $buf.=$cs[$i+3];
                    } else {
                        $buf='\e[31mE\e[0m';
                    }

                    if (is_numeric($cs[$i+4])) {
                        $buf.=$cs[$i+4];
                        if ($cs[$i+5] == "m") {
                            $buf.="m";
                            $buf.=$cs[$i+6];
                            $i+=6;
                        } else {
                            $buf='\e[31mE\e[0m';
                        }
                    } elseif ($cs[$i+4] == "m") {
                        $buf.="m";
                        $buf.=$cs[$i+5];
                        $i+=5;
                    }
                } else {
                    $buf=$cs[$i];
                }

                if (isset($this->buffers[$name]["content"][$index]) == false) {
                    $this->buffers[$name]["content"][$index]=[];
                }
                $this->buffers[$name]["content"][$index][$h-$rc] = $buf;
                $buf = "";
                $index +=1;
            } // for

            $rc += 1;
        } // foreach
    }

    /**
    * The function to draw the screen. We move the cursort to top left and just redraw everything.
    * No need to clear the screen. Removes the flickering.
    */
    public function drawBuffer()
    {
        $this->getScreenSize();
        
        $buffer = [];
        $buffer = $this->drawElement($buffer, "main");

        // move cursor to top
        system("printf '\033[;H'");

        //ob_start();
        for ($y = 0; $y <= $this->screen["height"]; ++$y) {
            $row = "";
            for ($x=0; $x < $this->screen["width"]; ++$x) {
                if (isset($buffer[$x])) {
                    if (isset($buffer[$x][$y])) {
                        $row .= $buffer[$x][$y];
                    } else {
                        $row .= " ";
                    }
                } else {
                    $row .= " ";
                }
            }
            echo $row."\n";
        }
    }
}
