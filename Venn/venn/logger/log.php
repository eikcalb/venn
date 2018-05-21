<?php

namespace Venn\logger;

/**
 * Description of log
 *
 * @author LORD AGWA    
 */
class Log {

    public $type, $message, $name, $level, $timestamp;

    public function __construct($type, $message, $name = "LOG", $level = Logger::LOG_LEVEL_DEFAULT) {
        $this->type = $type;
        $this->message = $message;
        $this->name = $name;
        $this->level = $level;
        $this->timestamp = time();
    }

}
