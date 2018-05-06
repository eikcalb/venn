<?php
namespace user;


interface UserInterface {
    
    
    public static function login($identifier,$pass);
    
    public function register();


    public function describe();
}
