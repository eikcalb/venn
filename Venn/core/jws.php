<?php

namespace core;

class JWS {

    private $payload;
    private $head, $body, $signature;

    private function __construct($header, $body, $pkey, $passphrase) {
        $this->head = $header;
        $this->body = $body;
    }

    /**
     * 
     * @param type $jose
     * @param type $signature
     * @param type $pkey
     * @param type $passphrase
     * @throws \Exception\JoseException
     */
    private function sign($jose, &$signature, $pkey, $passphrase = '') {
        $priv_key_id = openssl_pkey_get_private($pkey, $passphrase);
        if (!openssl_sign($jose, $signature, $priv_key_id, OPENSSL_ALGO_SHA256)) {
            throw new \Exception\JoseException(openssl_error_string(), \Exception\JoseException::CANNOT_SIGN);
        }
        $pkey_details = openssl_pkey_get_details($pkey);
        $pubkey = $pkey_details['key'];
        openssl_free_key($pkey);
        return $pubkey;
    }

    /**
     * 
     * @param array $head
     * @param array $body
     * @param type $pkey
     * @param type $passphrase
     * @return string token
     */
    public static function compose(array $head, array $body, $pkey, $passphrase = '') {
        $result = new JWS(json_encode($head), json_encode($body), $pkey, $passphrase);
        return $result;
    }

    public static function verify($token) {
        $parts = explode('.', $token);
        return openssl_verify($part, explode('.', $token)[2], openssl_pkey_get_public(file_get_contents(Kernel::$config['internal']['app_access_path'] . "/key")), OPENSSL_ALGO_SHA256);
    }

    public function getPayload() {
        $jose_head = base64_encode($this->head);
        $jose_body = base64_encode($this->body);
        $this->sign($jose_head . "." . $jose_body . ".", $this->signature, $pkey, $passphrase);
        $jose_signature = base64_encode($this->signature);
        $this->payload = $jose_head . "." . $jose_body . "." . $jose_signature;
        return $this->payload;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->head)) {
            return $this->head[$name];
        } elseif (array_key_exists($name, $this->body)) {
            return $this->body[$name];
        } elseif (strtolower($name) === "signature") {
            return $this->signature;
        }
    }

    public function __set($name, $value) {
        switch ($name) {
            case 'iat': $this->head[$name] = $value;
                break;
            case 'exp': $this->head[$name] = $value;
                break;
            case 'kid': $this->head[$name] = $value;
                break;
            case 'sub': $this->body[$name] = $value;
        }
    }

}
