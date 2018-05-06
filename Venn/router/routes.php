<?php

namespace request\route;

use request\route\Route;
use request\RequestParameters;

/**
 * This is the file where routes are registered to their controllers
 * 
 * It must be noted that routes are served on a first-in basis and therefore,
 * a good practice would be arranging your paths in a heirarchical form
 * 
 */
//------------------ LOGIN -----------
Route::get("/log", function() {
    var_dump($_SERVER);
});

Route::get("/login", "login");



//--------------------  REGISTER  ------------------
Route::GET("/register", function() {
    \controller\Register::start(filter_input_array(INPUT_GET, FILTER_DEFAULT, false));
});
Route::get("/core/launch", function() {
    
});

Route::get("/{flex}/{dwell}/{c}/l/", function(RequestParameters $a) {
    var_dump($a->path, Route::$state);
});

Route::get("/{name}/{gerdge}/{dad}/", function(RequestParameters $a) {
    var_dump($a);
});

Route::get("/", function(RequestParameters $r) {
    echo $r->host;
});


