<?php

namespace app\controller;

use Venn\controller\Controller;
use Venn\core\Kernel;

class Login extends Controller {

    private function __construct($data) {
        
    }

    public static function start($data) {
        set_error_handler(function($a, $b, $c, $d, $e) {
            var_dump($a, $b, $c, $d, $e);
        });
        \Venn\core\Kernel::genToken(['iat' => time() * 1000, 'kid' => 1], ['sub' => 5]);
            header("Authorization: Bearer fninDSDFdfdf$43r34d");

        if ($result) {
            $r = \Venn\core\Kernel::genToken(['iat' => time() * 1000, 'kid' => 1], ['sub' => $result->getFullName()]);
            var_dump($r, $result, \Venn\core\Kernel::verifyToken($r));
            header("Authorization: Bearer fninDSDFdfdf$43r34d");
        } else {
            echo 'Fail!';
        }
    }

    protected function render() {
        
    }

}
