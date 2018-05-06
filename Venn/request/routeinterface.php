<?php
namespace route;

interface RouteInterface {

    static function init($request,$path,$details);
    static function get();
    static function post();
    static function put();
    static function patch();
    static function head();
    static function delete();    
}
