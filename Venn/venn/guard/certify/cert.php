<?php
namespace guard\certify;

class Cert{
    
    //  TODO increase the days value
    private function create_csr($dn,&$privkey,$config){
        $csr=openssl_csr_new($dn, $privkey,$config);
        if($csr!=false&&!empty($csr)){
            return $csr;
        }
        throw new \Exception\CertException('',200);        
    }
    
    public static function gen($dn,$privkey,$cacert=null,$days=10,$config=null,$store=null,$filename=null){
        
        new Cert();
        $csr = $this->create_csr($dn, $privkey, $config);
        isset($store)?$default="$store/$filename/$filename.csr":$filename?$filename.'.csr': substr(time(),4).'.csr';
        if(openssl_csr_export_to_file($csr, $default)==false){
            throw new \Exception\CertException('',240);
        }
        $cert = $this->create_cert($csr, $cacert, $privkey, $days, $config);
        if(openssl_x509_export_to_file($cert,  str_ireplace(".csr", ".csr", $default) )==false){
            throw new \Exception\CertException('',400);
        }
        return $cert;
    }
    
    private function create_cert($csr,$cacert,&$privkey,$days,$config) {
        if(($cert=openssl_csr_sign($csr, $cacert, $privkey, $days,$config))==false){
            throw new \Exception\CertException('',500);
        }
        else{
            return $cert;
        }
    }
}
