<?php

use Venn\request\route\Route;
use Venn\request\RequestParameters;

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

Route::get("/sse", function(RequestParameters $rp) {
    /*
     * header("Content-Type: text/event-stream;charset=utf-8");
      header("Pragma: no-cache");
      header("Cache-Control: no-cache, must-revalidate, max-age=0");

      echo 'event: log' . PHP_EOL;
      echo 'data: ' . ( time() * 1000) . PHP_EOL;
      echo PHP_EOL . PHP_EOL;
      sleep(1);
     */

   $p = new Venn\core\event\EventEmitter();
    $j = function () {
        echo "Halleluyah!";
    };
    $p->once("lover", $j);
    $p->emit("lover");
    $p->emit("lover");
    $p->emit("lover");
});

Route::get("/login", "login");



//--------------------  REGISTER  ------------------
Route::GET("/register", function() {
    \Venn\controller\Register::start(filter_input_array(INPUT_GET, FILTER_DEFAULT, false));
});
Route::get("/core/launch", function($r) {
    echo serialize($r);
});

Route::get("/{flex}/{dwell}/{c}/l/", function(RequestParameters $a) {
    var_dump($a->path, Route::$state);
});

Route::get("/{name}/{gerdge}/{dad}/", function(RequestParameters $a) {
    var_dump($a);
});

Route::get("/", function(RequestParameters $r) {
    echo \Venn\core\View::render("index.php", ["lar" => ["va" => 'BarCode!']]);
});


