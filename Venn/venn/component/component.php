<?php

namespace Venn\component;

use Venn\controller\Controller;

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

    protected $name;
    // TODO: Remove $parent!!!!! 
    protected $parent;

    public static function start($data) {
        return new static;
    }

    protected function __construct($name) {
        if (!empty($name) && is_string($name)) {
            $fqn = get_class();
            $this->name = substr($fqn, strripos($fqn, "\\") + 1);
        }
        $this->bootstrap();
    }

    /**
     * Should be called before processing any request
     */
    protected function bootstrap() {
        
    }

    public function getName() {
        return $this->name;
    }

}
