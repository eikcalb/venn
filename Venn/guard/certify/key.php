<?php
namespace guard\certify;
require '../../autostrap.php';

class Key{
    private $config=null, $secret;
    private function __construct($config=null) {
        $this->config=$config;
    }
    private function create_key($outfile=null){
        if(empty($this->secret)||($pkey = openssl_pkey_new($this->config))==false){
            throw new \Exception\CertException('Cannot Create Key!',000);
        }
        
        if(!empty($outfile)){
            $output = \guard\File::fileCheck($outfile,"priv.pem");
            //  should not overwrite existing files
              var_dump($output);
              echo "\nDoing!\n\n $outfile";
            if($output['file']){
                echo 'Cannot Overwrite Existing Files';
                return $pkey;
            }elseif($output['dir']){
                $outfile=$output['_priv.pem']['file']?false:$output['_priv.pem']['name'];echo "\nDoin!\n\n $outfile";
            }else{
                $outfile = $output['name'];echo "\nDoig!\n\n $outfile";
            }echo "\nDoing!\n\n $outfile";
//            $outfile=$output['file']?false:$output['dir']?$output['_priv.pem']['file']?false:$output['_priv.pem']['name']:$outfile."/priv.pem";
            if(openssl_pkey_export_to_file($pkey, $outfile,$this->secret,  $this->config)===false){
                throw new \Exception\CertException('Cannot Create Private Key',004);
            }
            echo "\nDone!\n\n$outfile";
        }        
        return $pkey;
    }
        
    public static function gen($secret,$config=null,$store=null,$filename=null){
        $key = new Key($config);  echo "\nStart!\n\n";
        if(isset($store)){
            \guard\File::createLocalDir($store);
        }
        $key->secret=$secret;
        return $key->create_key($store.$filename);
    }
    public static function _public($cert){
        return openssl_csr_get_public_key($cert);
    }
}