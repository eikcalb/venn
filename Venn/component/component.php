<?php

namespace component;

use controller\Controller;
use core\Kernel;

/**
 * This is a container for connecting various parts of an applictaion.
 * 
 * The main entry point of an application.
 * There can be only one(1) root @see Component for every appliation.
 * Components contains methods to render views and general application configuration.
 * 
 * Components may make use of @see \controller\Controller objects for logic. *
 * 
 */
abstract class Component extends Controller {

    public $name;

    public static function start($data) {
        return new static;
    }

    public function __construct() {
        $fqn = get_class();
        $this->name = substr($fqn, strripos($fqn, "\\") + 1);
    }

    protected function bootstrap() {
        
    }

    public function route() {
        $resolvedRoute = Kernel::getRouter()->route();
    }

}
