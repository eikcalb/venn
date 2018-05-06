<?php

namespace controller;

/**
 * Loader represents the bare minimum criteria to load a @see Controller.
 *
 * @author LORD AGWA
 */
interface Loader {

    /**
     * This is called by @see Controller::fromLoader to setup the desired @see Controller object.
     */
    public function start();
}
