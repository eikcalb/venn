<?php
namespace guard;

class Crypto {
    private $key;
    public function __construct() {
        $config=["encrypt_key"=>true,
                "digest_alg"=>"sha256",
                "private_key_bits"=>2048,
                "encrypt_key"=>true,
                "config"=>"C:/Users/WENDY EZENWA/lord/xampp/php/extras/openssl.cnf"];
        $dn = ["countryName"=>"NG",
                "stateOrProvinceName"=>"Delta",
                "localityName"=>"Asaba",
                "organizationName"=>"BiOME",
                "organizationalUnitName"=>"BiOME Payment Engine",
                "commonName"=>"pay.biome.xyz",
                "emailAddress"=>"lord@pay.biome.xyz"];
        if($pkey=openssl_pkey_new($config)){
            $priv = openssl_pkey_get_private($pkey,"secret");echo $priv.'<p>';
        }else{
            echo __LINE__.'<p>'.openssl_error_string();
            }
        $details=openssl_pkey_get_details($pkey);
        $public = $details['key'];
        $csr = openssl_csr_new($dn, $priv,$config);echo '41'.$csr.'<p>';
        $card=  file_get_contents("C:\\Users\\WENDY EZENWA\\Desktop\\application\\win\\root.cer");
                $prv=openssl_pkey_get_private(file_get_contents("C:\\Users\\WENDY EZENWA\\Desktop\\application\\win\\root.pem"), "secret");
        if($new=openssl_csr_sign($csr, $card, $prv, 2,["x509extensions"=>"v3_req"]+$config)){            
            openssl_x509_export_to_file($new,"C:\\Users\\WENDY EZENWA\\Desktop\\application\\win\\paybot.cer");
            openssl_csr_export_to_file($csr, "C:\\Users\\WENDY EZENWA\\Desktop\\application\\win\\paybot.csr");
            openssl_pkey_export_to_file($pkey, "C:\\Users\\WENDY EZENWA\\Desktop\\application\\win\\paybot.pem", "secret",$config);
            return var_dump($csr, openssl_x509_parse($new,true));
        }
        while ($this->key=openssl_error_string()){
            echo $this->key;
        }
    }

    public function encrypt($plain,$key) {
        $cypher='';
        openssl_public_encrypt($plain,$cypher,$key);
        return $cypher;
    }
    public function decrypt(...$cypher){
        $private_key = $this->get_private($cypher[0]);
        $decrypted = '';
        openssl_private_decrypt($cypher[1], $decrypted, $private_key);
        return $decrypted;
    }
    private function get_private($priv_key){
        $enc_priv = file_get_contents($priv_key);
        return base64_decode($enc_priv);
    }
}
