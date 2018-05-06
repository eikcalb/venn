<?php

namespace request;

class RequestParameters {

    public $verb, $path, $host, $port, $protocol, $queryString;
    public $matchedPath;
    private $headers;
    private $params = [];

    public function __construct($verb, $protocol, $host, $path, array $headers) {
        $this->verb = $verb;
        $this->path = $path;
        $this->host = $host;
        $this->protocol = $protocol;
        $this->headers = $headers;
    }

    public function addHeader($name, $value, $replace = true) {
        if (!is_string($name) || !is_string($value)) {
            return false;
        }
        if (array_key_exists($name, $this->headers)) {
            if ($replace) {
                $this->headers[$name] = $value;
            } else {
                $temp = is_array($this->headers[$name]) ? $this->headers[$name] : [$this->headers[$name]];
                $temp[] = $value;
                $this->headers[$name] = $temp;
                return true;
            }
        } else {
            $this->headers[$name] = $value;
            return true;
        }
    }

    public function getHeader($name, $multipleAsArray = false) {
        if (empty($this->headers) || !array_key_exists($name, $this->headers)) {
            return null;
        }
        $result = $this->headers[$name];
        if (!$multipleAsArray) {
            return is_array($result) ? $result[count($result) - 1] : $result;
        } else {
            return $result;
        }
    }
    
    public function addParam($name, $value, $replace = false) {
        if (!is_string($name) || !is_string($value) || !is_bool($replace)) {
            return false;
        }
        if(array_key_exists($name,  $this->params)){
            if($replace){
                $this->params[$name] = $value;
            } else {
                return false;
            }
        }
        $this->params[$name] = $value;
        return true;
    }

    public function getParam($name) {
        if (!array_key_exists($name, $this->params)) {
            return null;
        } else {
            return $this->params[$name];
        }
    }

    public function removeParam($name) {
        if (!is_string($name)) {
            return false;
        }
        if (!array_key_exists($name, $this->params)) {
            return false;
        }
        $this->params[$name] = null;
        unset($this->params[$name]);
        return true;
    }

    public function setParam($name, $value) {
        if (!is_string($name) || !is_string($value)) {
            return false;
        }
        if (array_key_exists($name, $this->params)) {
            $this->params[$name] = $value;
        } else {
            return false;
        }
    }

    public function addParams($params) {
        if (!is_array($params)) {
            return false;
        }
        $this->params = $params;
        return false;
    }

    public function clearParams() {
        $this->params = [];
    }
    
    public function getParams() {
        return $this->params;
    }

}
