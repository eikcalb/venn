<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io> 2018
 *  Part of Venn
 */

namespace Venn\core\event;

/**
 * Description of ClosureEvent
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
class ClosureEventListener {

    protected $closure;

    public function __construct(\Closure $closure) {
        $this->closure = $closure->bindTo($this);
    }

    public function getClosure() {
        return $this->closure;
    }

}
