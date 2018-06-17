<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io> 2018
 *  Part of Venn
 */

namespace Venn\core\event;

/**
 * Description of EventEmitter
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
class EventEmitter {

    protected $usePrefix = true;
    protected $prefix = __CLASS__;
    protected static $eventManager;

    public function __construct($prefix = null) {
        if (!empty($prefix)) {
            $this->prefix = $prefix;
        }
        if (empty(static::$eventManager)) {
            static::$eventManager = new \Symfony\Component\EventDispatcher\EventDispatcher();
        }
    }

    public function addEventListener($event, callable $callable, $priority = 0) {
        if ($this->usePrefix) {
            $this->getEventManager()->addListener("{$this->prefix}.{$event}", $callable, $priority);
        } else {
            $this->getEventManager()->addListener($event, $callable, $priority);
        }
    }

    public function emit($event, Event $eventObject = null) {
        if ($this->usePrefix) {
            $this->getEventManager()->dispatch("{$this->prefix}.{$event}", $eventObject);
        } else {
            $this->getEventManager()->dispatch($event, $eventObject);
        }
    }

    private function getEventManager() {
        return static::$eventManager;
    }

    public function on($event, callable $listener) {
        $this->addEventListener($event, $listener);
    }

    public function once($event, callable $listener) {
        $host = &$this;
        $proxy = new ClosureEventListener(function() use (&$event, &$host, &$listener) {
            $host->removeEventListener($event, $this->getClosure());
            call_user_func_array($listener, func_get_args());
        });
        $this->addEventListener($event, $proxy->getClosure());
    }

    public function off($event, $listener) {
        $this->removeEventListener($event, $listener);
    }

    public function removeEventListener($event, $listener) {
        if ($this->usePrefix) {
            $this->getEventManager()->removeListener("{$this->prefix}.{$event}", $listener);
        } else {
            $this->getEventManager()->removeListener($event, $listener);
        }
    }

}
