<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io>
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace request\route;

/**
 * Description of routemodifier
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
class RouteFilter {

    private static $acceptedModifiers = ['startswith'];

    public static function hasModifier($modifier) {
        if (in_array($modifier, static::$acceptedModifiers)) {
            return true;
        } else {
            return false;
        }
    }

    public static function modify(Route $route, $modifier, $args) {
        if (!static::hasModifier($modifier)) {
            throw new \BadFunctionCallException("Function '{$name}' is not supported!");
        }
        throw new \BadFunctionCallException("Not yet implemented!:(");
        return $route;
    }

}
