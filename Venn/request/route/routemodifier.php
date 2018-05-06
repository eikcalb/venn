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
class RouteModifier {
    private static $acceptedModifiers = ['startswith'];

    public static function hasModifier($modifier) {
        if (in_array($modifier, static::$acceptedModifiers)) {
            return true;
        } else {
            return false;
        }
    }

}
