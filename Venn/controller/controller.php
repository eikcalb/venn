<?php
namespace controller;

/**
 * Represents a logical unit of an application.
 * The recommended way to get use controller instances is via respective @see \core\Kernel methods, e.g. @see \core\Kernel::loadController($controller, $data).
 * 
 * Controllers may also provide a accept a @see Loader by overriding the @see Controller::fromLoader($loader) method.
 * 
 * Subclasses should provide specific @see Loader classes for their consumption.
 */
abstract class Controller {
    public abstract static function start($data);
    
    public static function fromLoader(Loader $loader){
        throw new \Exception\ControllerException("Unsupported method invoked!");
    }
}
