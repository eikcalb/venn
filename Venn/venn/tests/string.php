<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io>
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function lump() {
    $time = microtime(true);
    $lag = 'c/c/{lag}cc';
    $count = 100000000;
    while ($count > 0) {
        $count--;
        if (stripos($lag, '{') != false) {
            
        }
        stripos($lag, '}');
    }echo microtime(true) - $time . "<p>";
}

function chump() {
    $time = microtime(true);
    $lag = 'c/c/{lag}cc';
    $count = 100000000;
    while ($count > 0) {
        $count--;
        if (preg_match("/{.+}/", $lag)) {
            
        }
        preg_match("/{.+}/", $lag);
    }echo microtime(true) - $time . "<p>";
}

for ($i = 0; $i < 4; $i++) {
    filter_input(INPUT_GET, 'l') === 'p' ? chump() : lump();
}