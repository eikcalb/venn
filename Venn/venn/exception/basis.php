<?php
namespace Venn\Exception;

class Basis extends \Exception {
    /**
     * @var \Exception
     */
    protected $message;
    private $string;
    protected $code;
    protected $file;
    protected $line;
    private $trace;
    private $previous;
    
    public function __construct ($message = "", $code = 0, \Exception $previous = null) {            
            $this->message = $message;
            $this->code = $code;
            $this->previous = $previous;
            parent::__construct();
    }
    public function getName(){
        $fqn=  get_class($this);
        return substr($fqn,strripos($fqn,"\\")+1);
    }
}
