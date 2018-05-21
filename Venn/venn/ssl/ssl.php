<?php
namespace Venn\ssl;

class CurlSSl{
    private $handle;
    public function __construct($handle,$cert,$priv,$ca,$passphrase,$password=null) {
        $this->curl = $handle;
        if($this->cert($cert,$password)&&$this->pkey($priv, $passphrase)&&$this->certAuth($ca)){
            
            return true;// this could fault , instead create a static function
        }  else {
            throw new \Exception\CertException("Curl certificate error",900);
        }
    }
    
    private function certAuth($ca){
        if(is_file($ca)){//   additional check layer to prevent CA failure
            return $this->trustedCAFile($ca);
        }
        elseif(is_dir($ca)){//   additional check layer to prevent CA failure
            return $this->trustedCAPath($ca);
        }
    }

    private function cert($certificate,$password){
        return curl_setopt($this->handle,CURLOPT_SSLCERT,$certificate) && curl_setopt($this->handle,CURLOPT_SSLCERTPASSWD,$password);
    }
    
    private function pkey($priv_key,$passphrase){
        return curl_setopt($this->handle,CURLOPT_SSLKEY,$priv_key) && curl_setopt($this->handle,CURLOPT_SSLKEYPASSWD,$passphrase);
    }
    
    private function trustedCAFile($cafile){
        return curl_setopt($this->handle,CURLOPT_CAINFO,$cafile);
    }
    
    private function trustedCAPath($capath){        
        return curl_setopt($this->handle,CURLOPT_CAPATH,$capath);
    }
}