<?php

namespace Venn\logger;

abstract class Formatter{
    abstract function format(Log $data);
}

