<?php

namespace logger;

abstract class Formatter{
    abstract function format(Log $data);
}

