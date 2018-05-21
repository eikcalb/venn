<?php
namespace Venn\controller;

class Register extends Controller{
    
    public function __construct() {
        ;
    }


    public static function start($args) {
        $new_user= new \user\User($args);
        $result = $new_user->register();        
        if($result){
            echo $result;
        }else{
            echo 'Fail!';
        }
    }

}
