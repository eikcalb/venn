<?php

namespace Venn\controller;

/**
 * Represents a logical unit of an application.
 * The recommended way to get use controller instances is via respective @see \core\Kernel methods, e.g. @see \core\Kernel::loadController($controller, $data).
 * 
 * Controllers may also provide and accept a @see Loader by overriding the @see Controller::fromLoader($loader) method.
 * 
 * Subclasses should provide specific @see Loader classes for their consumption.
 */
abstract class Controller {

    public abstract static function start($data);

    public static function fromLoader(Loader $loader) {
        throw new \Exception\ControllerException("Unsupported method invoked!");
    }

    protected abstract function render();

    /**
     * 
     * @param type $controller <p>The action designated for the detected url</p>
     *                          <p>This function is called with the HTTP Method and @see Ref::queue results</p>
     * 
     */
    public static function call_controller($controller, $args) {
        if (empty($controller)) {
            throw new \Venn\Exception\ControllerException("Controller must be set for handling routes!");
        }
        if (is_callable($controller)) {
            return $controller($args);
        } elseif (is_string($controller)) {
            return Kernel::loadController($controller, $args);
        }
    }

}
